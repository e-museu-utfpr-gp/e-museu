# Coolify GitHub polling auto-deploy

Optional flow: a **scheduled task** inside the Coolify app container runs a shell script that compares the current GitHub branch tip with a file under `storage/coolify/`. When the SHA changes, it calls the Coolify **deploy API** with a **Bearer token**, then saves the new SHA so the same commit is not deployed twice.

## Prerequisites

1. **Coolify API token** with **deploy** permission: Coolify UI → **Security or Keys & Tokens** → **API Tokens** (`/security/api-tokens`). Set **Expires** to **Never** for long-lived automation (rotate if compromised).
2. **Deploy URL** for the resource: full URL including query string, for example  
   `http://YOUR_COOLIFY_HOST:8000/api/v1/deploy?uuid=RESOURCE_UUID&force=false`  
   (`force` stays in the URL as you configure it.)
3. Container image must include **git** and **curl** (staging/production Dockerfiles in this repo already install them).

## Environment variables

Set these on the **same** Coolify resource (staging or production) whose container runs the task:

| Variable | Description |
|----------|-------------|
| `COOLIFY_AUTO_DEPLOY_GITHUB_REPO_URL` | Git clone URL (e.g. `https://github.com/e-museu-utfpr-gp/e-museu.git`) |
| `COOLIFY_AUTO_DEPLOY_GITHUB_BRANCH` | Branch to watch (e.g. `develop` for staging, `main` for production) |
| `COOLIFY_AUTO_DEPLOY_DEPLOY_URL` | Full Coolify deploy API URL including `uuid` and query flags |
| `COOLIFY_AUTO_DEPLOY_TOKEN` | Secret token; use Coolify secrets, never commit |

`APP_ENV` must be **`staging`** or **`production`** so the script picks the correct state file:

- `storage/coolify/last_sha_github_repo_staging`
- `storage/coolify/last_sha_github_repo_production`

## Script and command

Script path in the image: **`/var/www/scripts/coolify-auto-deploy.sh`**

Use a scheduled task **command** like:

```sh
/bin/sh -lc '/var/www/scripts/coolify-auto-deploy.sh'
```

## Coolify scheduled task (UI)

Create **Scheduled Tasks** on each resource (staging and production):

| Field | Staging | Production |
|-------|---------|------------|
| **Name** | e.g. `GitHub poll → Coolify deploy` | same pattern |
| **Command** | `/bin/sh -lc '/var/www/scripts/coolify-auto-deploy.sh'` | same |
| **Frequency** | `*/5 * * * *` (every 5 minutes) | `*/10 * * * *` (every 10 minutes) |
| **Timeout (seconds)** | `300` (or higher if needed) | same |
| **Container** | staging app container (e.g. `app-staging`) | production app container |

Staggering (5 min vs 10 min) reduces the chance of two deploys firing back-to-back when both branches move.

## Templates

Paste-friendly env keys are listed in:

- `docs/deploy-coolify/coolify-staging.env.example`
- `docs/deploy-coolify/coolify-production.env.example`

## Behaviour notes

- The SHA file is updated **after** a successful HTTP response from the deploy endpoint, so a failed request will retry on the next run.
- State files are gitignored under `storage/coolify/`; only the directory is kept in the repo (`.gitkeep`).
