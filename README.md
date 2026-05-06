# E-Museu

E-Museu is a digital museum platform inspired by electronic waste projects led by The Federal University of
Technology - Parana (UTFPR) and the State University of Central-West (Unicentro). These projects collect
unused computer parts and motivated the creation of a museum to preserve their history, with contributions
from both project members and the broader community.

- **Version:** `1.0.0`
- **Original forks:** [vinifen/e-museu-2.1.0-beta](https://github.com/vinifen/e-museu-2.1.0-beta), [tankesho/e-museu](https://github.com/tankesho/e-museu)

---

## 🧰 Tech Stack

- Laravel (PHP), Blade + Vite
- MySQL 8 (required)
- Redis
- Docker / Docker Compose
- Node.js (frontend tooling)

---

## 🚀 Quick Start

### 1) ✅ Prerequisites

- Docker + Docker Compose
- Git

Optional (to avoid `sudo` on Docker commands):

```bash
sudo usermod -aG docker $USER
newgrp docker
```

### 2) ⚙️ Set up the project

```bash
./run setup
```
This command creates `.env` if missing, starts containers, installs dependencies, runs migrations/seeders, and starts Vite in local mode.

If you need a clean re-setup while keeping your local DB volume:

```bash
./run setup-hard -y -db
```

### 3) 🌐 Access app

- Local: `http://localhost:9090` (default from `.env.example`)

---

## 🧭 Environment Modes

Environment is selected by `APP_ENV`:

- `local` -> `docker-compose.local.yml`
- `prod-local` -> `docker-compose.prod-local.yml` (production-like local run)

Use `.env.example` as reference for all variables.

---

## 🎛️ Feature Toggles (`.env`)

Enable/disable services explicitly per environment:

### ✉️ Public collaborator email verification

```env
MAIL_PUBLIC_CONTRIBUTION_EMAIL_VERIFICATION_ENABLED=false
```

- `true`: public catalog contribution requires request/confirm email-code session.
- `false`: email verification UI/routes are disabled for contribution flow.

### 🛡️ Anti-bot (Turnstile)

```env
ANTIBOT_DRIVER=null
# ANTIBOT_DRIVER=turnstile
TURNSTILE_SITE_KEY=
TURNSTILE_SECRET_KEY=
```

- `ANTIBOT_DRIVER=null`: disabled
- `ANTIBOT_DRIVER=turnstile`: enabled (requires both keys)

### 🤖 Admin AI providers

At least one provider must be enabled for admin translation assist:

```env
OPENROUTER_ENABLED=false
GROQ_ENABLED=false
GITHUB_MODELS_ENABLED=false
```

When enabled, set credentials/models for each provider (`*_API_KEY` / `*_TOKEN`, models and provider URL).

---

## 🧪 Core Commands (`./run`)

### 🐳 Containers

```bash
./run up
./run down
./run ps
```

### ♻️ Setup / Reset

```bash
./run setup
./run setup -y
./run setup-hard -y
./run setup-hard -y -db
```

`setup-hard` removes containers, dependencies, images, and volumes.  
Use `-db` to keep the local MySQL volume (`mysql_data`).

### 🗄️ Database

```bash
./run migrate
./run seed
```

### 🎨 Frontend

```bash
./run npm-local-bg
./run stop-vite-local
./run start-vite-local
./run build-vite
```

### ✅ Testing and Quality

```bash
./run test
./run phpstan
./run phpcs
./run phpcbf
./run phpinsights
./run phpcpd
./run churn
./run phpmd
./run eslint
./run prettier
./run all-tests
```

### 🧩 Utility

```bash
./run help
./run exec php artisan tinker
./run remove-all -y
./run remove-all -y -db
```

---

## 📌 Database Notes (MySQL Required)

This project relies on MySQL-specific behavior (for example locale ordering with SQL `FIELD()`), so SQLite is not supported in those paths.

`phpunit.xml` uses `DB_DATABASE=emuseu_testing` to isolate tests from development data.  
`./run test` ensures this test database exists and grants proper permissions.

---

## Mail Configuration

Mail defaults to SMTP (`config/mail.php`).  
Set at least:

```env
MAIL_MAILER=smtp
MAIL_HOST=...
MAIL_PORT=587
MAIL_USERNAME=...
MAIL_PASSWORD=...
MAIL_FROM_ADDRESS=...
MAIL_FROM_NAME="${APP_NAME}"
```

### Public contribution email verification toggle

Public catalog contribution email verification is feature-flagged:

```env
MAIL_PUBLIC_CONTRIBUTION_EMAIL_VERIFICATION_ENABLED=false
```

- `true`: requires request/confirm code flow
- `false`: hides verification flow and allows contribution without email-code session

---

## 🚢 Deploy References

Deployment env templates and docs:

- `docs/deploy/coolify-production.env.example`
- `docs/deploy/coolify-staging.env.example`
- `docs/deploy/coolify-minio-s3/`

### ☁️ Coolify support (MySQL, Redis, MinIO/S3)

This project supports Coolify deployment with MySQL, Redis, and S3-compatible object storage (MinIO).

Main env points:

- `DATABASE_URL` -> MySQL DSN
- `REDIS_URL` -> Redis connection
- `FILESYSTEM_DISK=s3` + `AWS_*` -> MinIO/S3 storage (`AWS_ENDPOINT`, credentials, bucket)

Use:

- `docs/deploy/coolify-production.env.example`
- `docs/deploy/coolify-staging.env.example`
- `docs/deploy/coolify-minio-s3/`

---

## 🌍 Content/Locale Maintenance

When adding a new content language, update all related layers together:

1. `App\Enums\Content\ContentLanguage`
2. `languages` seed/migration
3. `lang/{locale}/...` server translations
4. `lang/js/{locale}.json` + loader in `resources/js/i18n.js`

For catalog locations, keep `locations.code` and translation keys in sync (`lang/{locale}/app/catalog.php` -> `app.catalog.location.codes.*`).

---

## 🧯 Troubleshooting

### 🐳 Docker permission denied

```bash
groups | grep docker
ls -l /var/run/docker.sock
```

If needed, run `newgrp docker` or sign out/in.

### 🗄️ MySQL not ready

First startup can take 10-30 seconds. `./run` commands already wait for readiness where needed.

### 🔌 Port already in use

```bash
sudo lsof -i :3306
sudo netstat -tlnp | grep :3306
```

Adjust ports in `.env` when necessary.

---

## 📚 Project Docs

- Product requirements: `docs/prd.md`
- Software design: `docs/sdd.md`
- Database model diagram (Mermaid): `docs/database/database-model.md`

