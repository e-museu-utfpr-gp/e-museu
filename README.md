# E-Museu TEST CI

#### Forked from https://github.com/tankesho/e-museu

#### v5.0.1-beta

# 🚧 Work In Progress 

# Installation and Setup Guide

This guide describes how to configure the development environment and initialize the E-Museu project.

## Prerequisites

- Docker and Docker Compose installed
- Git (for cloning the repository)

## Database (MySQL)

The application targets **MySQL** in development, staging, and production. Catalog translations and admin listings rely on MySQL-specific SQL (for example `FIELD()` for locale fallback order). **SQLite is not supported** for those code paths.

Automated tests under the **`mysql` group** (for example `tests/Feature/Catalog/TranslationResolutionTest.php`) **require** `DB_CONNECTION=mysql` and a migrated database. If you run PHPUnit without MySQL, those tests are skipped.

### PHPUnit database `emuseu_testing`

`phpunit.xml` sets `DB_DATABASE=emuseu_testing` so tests do not wipe your development database.

- **`./run test`** runs `ensure-mysql-testing-database` first: it creates `emuseu_testing` and grants it to the application user using credentials **inside** the `db` container (`MYSQL_ROOT_PASSWORD` / `MYSQL_USER`, derived from `.env`).
- On the **first** MySQL volume initialization, `docker/mysql/init/01-create-testing-database.sh` may also create that database; if your `mysql_data` volume was created earlier, rely on `./run test` (or run the same SQL manually) so the database and `GRANT` exist.

### Adding a catalog / content language

A new language is not only a row in `languages`. To keep SQL `FIELD()`, PHP fallback, admin forms, and UI packs aligned:

1. Add the case to `App\Enums\Content\ContentLanguage` (and `orderedNonUniversalLocales()` / form ordering if it should participate in fallback priority).
2. Seed the `languages` table (migration or seeder) with a stable `code` matching the enum value.
3. Add Laravel translation files under `lang/{code}/` (at minimum what you expose in the locale switcher).
4. For strings used by i18next on the client, add `lang/js/{code}.json` and register the dynamic import in `resources/js/i18n.js` (`bundleLoaders`).

Skipping any of the above leaves the language missing from `ContentLocaleFallback::orderedCodes()` or without UI strings until those pieces are updated.

### Catalog locations (`locations` table)

Items reference a **location** (campus / site). Rows are **seeded reference data** with a stable uppercase `code` (for example `INDEF`, `UTFPR`, `UNCEN`). The label shown in forms and listings comes from translation keys under `app.catalog.location.codes.*` in `lang/{locale}/app/catalog.php` (with fallback to the row `name` when a key is missing). After changing codes or adding a location, update seeds and those translation entries together.

## Outgoing mail (Resend)

Production-oriented outbound mail uses the **Resend** driver by default (`config/mail.php`, `MAIL_MAILER=resend`). Set `RESEND_KEY` in `.env` (see `config/services.php`). The helper `App\Support\Mail\OutgoingMailIsConfigured` decides whether the app should attempt verification and notification e-mails; extend `mail.transport_required_config` when adding new transports.

- **Deploy:** set `MAIL_MAILER`, `RESEND_KEY`, `MAIL_FROM_ADDRESS`, and `MAIL_FROM_NAME` (see `.env.example`).
- **Tests:** `phpunit.xml` sets `MAIL_MAILER=array` so PHPUnit does not call Resend. Unit coverage for mail readiness lives in `tests/Unit/Support/Mail/OutgoingMailIsConfiguredTest.php`.

## Initial Docker Configuration (Avoid using sudo)

By default, Docker commands require administrator privileges (sudo). To avoid having to use `sudo` for every command, add your user to the `docker` group:

### 1. Add user to docker group

Execute the following command (you'll need to use sudo one last time):

```bash
sudo usermod -aG docker $USER
```

### 2. Apply changes

You need to reload the groups. You have two options:

**Option A: Reload in current session (recommended for testing)**
```bash
newgrp docker
```

This starts a new session with the docker group active. You can exit this session with `exit` when you're done.

**Option B: Logout and login again**
- Close the terminal and open a new one, or
- Logout from the system session and login again

### 3. Verify it worked

Execute to verify you're in the docker group:

```bash
groups
```

You should see `docker` in the group list. Now Docker commands will work without sudo!

---

## Project Initialization

The project uses a `./run` script to facilitate command execution. All operations are done through this script.

### First Time Setup

#### Option 1: Complete Setup (Recommended)

To perform a complete project setup (creates `.env` if missing, starts containers, installs dependencies, runs migrations and seeders):

```bash
./run setup
```

This command:
- Ensures `.env` exists (copies from `.env.example` only if `.env` is missing; use `./run env -f` to replace `.env` from the example)
- Starts Docker containers
- Installs Composer dependencies
- Generates `APP_KEY` only when it is not already set in `.env`
- Runs database migrations
- Runs seeders (initial data)
- In local environment, starts Vite server

#### Option 2: Setup with automatic confirmation (Production)

For production environments or automation, use the `-y` flag to skip confirmations:

```bash
./run setup -y
```

#### Option 3: Complete Reset (Clean everything and start over)

If you want to start from scratch, removing all data, containers and generated files:

```bash
./run setup-hard
```

**Warning:** This command removes:
- Database (mysql_data)
- Dependencies (vendor, node_modules)
- Docker containers
- Docker images
- Docker volumes

To skip the confirmation:

```bash
./run setup-hard -y
```

To keep the local MySQL data directory (`mysql_data`) while still resetting containers, vendor, and Node assets (useful when you do not want to lose the database volume):

```bash
./run setup-hard -y -db
# or
./run setup-hard -db -y
```

`setup` / `setup-hard` never overwrite an existing `.env` from `.env.example`; they only create it when the file is missing. The same `-db` flag works with `./run remove-all` and `./run remove-all-files` (e.g. `./run remove-all -y -db`). The flag `-env` is still accepted for backwards compatibility but does nothing extra (older docs used `./run setup -env`).

### Main Commands

#### Container Management

```bash
# Start containers
./run up

# Stop containers
./run down

# View container status
./run ps
```

#### Database

```bash
# Run migrations
./run migrate

# Run seeders
./run seed
```

#### Dependencies

```bash
# Install Composer dependencies
./run composer

# Generate application key (use -y in production)
./run gen_key
# or
./run gen_key -y  # to skip confirmation
```

#### Local Environment (Development)

```bash
# Install Node dependencies and start Vite
./run npm-local-bg

# Stop Vite
./run stop-vite-local

# Start Vite again
./run start-vite-local

# Build assets for production
./run build-vite
```

#### Testing and Code Quality

**Important:** Before running code quality tools:
1. Make sure `APP_ENV=local` is set in your `.env` file
2. Run `./run setup` to start the containers and ensure the vendor directory is properly mounted and the binaries are available

```bash
# Setup project first (required for code quality tools)
./run setup

# Run tests (creates/grants emuseu_testing when using Docker MySQL — see "PHPUnit database emuseu_testing" above)
./run test

# Analyze code (PHPStan)
./run phpstan

# Check code standards (PHPCS)
./run phpcs

# Fix code standards (PHPCBF)
./run phpcbf

# PHP Insights (code quality analysis)
./run phpinsights

# Detect duplicated code (PHPCPD)
./run phpcpd

# Analyze refactoring candidates (Churn)
./run churn

# PHP Mess Detector
./run phpmd

# ESLint (JavaScript linting)
./run eslint

# Prettier (code formatting)
./run prettier

# Run all tests and checks
./run all-tests
```

#### Utilities

```bash
# Show complete help
./run help

# Execute command inside container
./run exec php artisan tinker

# Clean environment (no confirmation with -y)
./run remove-all -y

# Full cleanup but keep local MySQL volume (mysql_data)
./run remove-all -y -db
```

---

## Environments

The script automatically detects the environment based on the `APP_ENV` variable in the `.env` file:

- `APP_ENV=local` → Uses `docker-compose.local.yml`
- `APP_ENV=prod-local` → Uses `docker-compose.prod-local.yml` (local production simulation)

### .env Configuration

Make sure the `.env` file is configured correctly. Important variables include:

**For local and prod-local environments:**
```env
APP_ENV=local  # or prod-local
DB_HOST=db
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=user
DB_PASSWORD=your_password
```

---

## Troubleshooting

### Docker permission error

If you still get permission errors after adding to the docker group:

1. Verify you're in the group: `groups | grep docker`
2. If not, execute `newgrp docker` or logout/login
3. Check socket permissions: `ls -l /var/run/docker.sock`

### Database container won't start

MySQL may take a few seconds to start, especially the first time. The script automatically waits for the database to be ready before running migrations.

### Port already in use

If you get a port in use error (especially 3306), check:

```bash
# See what's using port 3306
sudo lsof -i :3306
# or
sudo netstat -tlnp | grep :3306
```

Stop the local MySQL service if necessary, or adjust the port in `.env`.

---

## Important Notes

- **First initialization**: MySQL may take 10-30 seconds to start the first time
- **Production environment**: Always use the `-y` flag in automated scripts to skip confirmations
- **Docker group**: After adding your user to the docker group, you no longer need to use sudo
- **Complete reset**: `setup-hard` removes ALL data by default. Use with caution! Pass **`-db`** to keep `mysql_data` (see Option 3 above).

---

## Quick Reference Commands

```bash
# Complete initial setup
./run setup

# Setup with automatic confirmation
./run setup -y

# Complete reset (careful!)
./run setup-hard -y

# Hard reset but keep local DB volume
./run setup-hard -y -db

# Start containers
./run up

# Stop containers
./run down

# Show help
./run help
```
