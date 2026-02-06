# E-Museu

#### Forked from https://github.com/tankesho/e-museu

#### v2.1.0-beta

# 🚧 Work In Progress 

# Installation and Setup Guide

This guide describes how to configure the development environment and initialize the E-Museu project.

## Prerequisites

- Docker and Docker Compose installed
- Git (for cloning the repository)

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

To perform a complete project setup (creates `.env`, starts containers, installs dependencies, runs migrations and seeders):

```bash
./run setup -env
```

This command:
- Copies `.env.example` to `.env`
- Starts Docker containers
- Installs Composer dependencies
- Generates Laravel application key
- Runs database migrations
- Runs seeders (initial data)
- In local environment, starts Vite server

#### Option 2: Setup with automatic confirmation (Production)

For production environments or automation, use the `-y` flag to skip confirmations:

```bash
./run setup -env -y
```

#### Option 3: Complete Reset (Clean everything and start over)

If you want to start from scratch, removing all data, containers and generated files:

```bash
./run setup-hard -env
```

**Warning:** This command removes:
- Database (mysql_data)
- Dependencies (vendor, node_modules)
- Docker containers
- Docker images
- Docker volumes

To skip the confirmation:

```bash
./run setup-hard -env -y
```

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

# Run tests
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
- **Complete reset**: `setup-hard` removes ALL data. Use with caution!

---

## Quick Reference Commands

```bash
# Complete initial setup
./run setup -env

# Setup with automatic confirmation
./run setup -env -y

# Complete reset (careful!)
./run setup-hard -env -y

# Start containers
./run up

# Stop containers
./run down

# Show help
./run help
```
