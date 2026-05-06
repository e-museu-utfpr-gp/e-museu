# Coolify GitHub polling auto-deploy

Optional flow: a **scheduled task** inside the Coolify app container runs a shell script that compares the current GitHub branch tip with a file under `storage/coolify/`. When the SHA changes, it calls the Coolify **deploy API** with a **Bearer token**, then saves the new SHA so the same commit is not deployed twice.

## Prerequisites

1. **Coolify API token** with **deploy** permission: Coolify UI → **Security or Keys & Tokens** → **API Tokens** (`/security/api-tokens`). Set **Expires** to **Never** for long-lived automation (rotate if compromised).
2. **Deploy URL** for the resource: full URL including query string, for example  
   `http://YOUR_COOLIFY_HOST:8000/api/v1/deploy?uuid=RESOURCE_UUID&force=false`  
   (`force` stays in the URL as you configure it.)
3. Container image must include **git** and **curl** (staging/production Dockerfiles in this repo already install them).

## Environment variables

Set these on the **same** Coolify resource (staging or production) whose **app** container runs the task (not the nginx/web-only container). **Redeploy** after changing variables so the running container receives them.

### Coolify UI / YAML: `&` and `|`

Coolify stores env in **YAML**. In unquoted values, **`&`** starts a YAML anchor and **`|`** starts a literal block. That often yields **empty or wrong** variables inside the container — the script then reports *missing required env*.

**Recommended:** use **origin + uuid + force** (no `&` in any value):

| Variable | Example |
|----------|---------|
| `COOLIFY_AUTO_DEPLOY_COOLIFY_ORIGIN` | `http://YOUR_COOLIFY_HOST:8000` |
| `COOLIFY_AUTO_DEPLOY_RESOURCE_UUID` | your resource uuid from the deploy API link |
| `COOLIFY_AUTO_DEPLOY_FORCE` | `false` |

The script builds `…/api/v1/deploy?uuid=…&force=…` internally.

**Alternative:** set `COOLIFY_AUTO_DEPLOY_DEPLOY_URL` to the full URL but wrap it in **double quotes** in the Coolify env value.

Tokens often contain **`|`**; if the full URL or token is wrong in the container, quote the value or use **`COOLIFY_AUTO_DEPLOY_TOKEN_FILE`** (path to a file readable in the container, content = token only).

### Full list

| Variable | Description |
|----------|-------------|
| `COOLIFY_AUTO_DEPLOY_GITHUB_REPO_URL` | Git clone URL (e.g. `https://github.com/e-museu-utfpr-gp/e-museu.git`) |
| `COOLIFY_AUTO_DEPLOY_GITHUB_BRANCH` | Branch to watch (e.g. `develop` / `main`) |
| `COOLIFY_AUTO_DEPLOY_COOLIFY_ORIGIN` | Coolify base URL, no path (use with `RESOURCE_UUID`) |
| `COOLIFY_AUTO_DEPLOY_RESOURCE_UUID` | Resource uuid from the deploy link |
| `COOLIFY_AUTO_DEPLOY_FORCE` | Optional; `true` or `false` (default `false`) |
| `COOLIFY_AUTO_DEPLOY_DEPLOY_URL` | Optional full deploy URL if you do not use the three vars above |
| `COOLIFY_AUTO_DEPLOY_TOKEN` | API Bearer token |
| `COOLIFY_AUTO_DEPLOY_TOKEN_FILE` | Optional; token file path if `TOKEN` is empty |

`APP_ENV` must be **`staging`** or **`production`** so the script picks the correct state file:

- `storage/coolify/last_sha_github_repo_staging`
- `storage/coolify/last_sha_github_repo_production`

## Script and command

Script path in the image: **`/var/www/scripts/coolify-auto-deploy.sh`**

Use a scheduled task **command** like (no quotes — Coolify often runs your input inside `sh -c '…'`, and nested `'` causes `unterminated quoted string`):

```text
/var/www/scripts/coolify-auto-deploy.sh
```

Fallback if the executable bit is missing in a custom image: `/bin/sh /var/www/scripts/coolify-auto-deploy.sh`

## Coolify scheduled task (UI)

Create **Scheduled Tasks** on each resource (staging and production):

| Field | Staging | Production |
|-------|---------|------------|
| **Name** | e.g. `Staging / GitHub poll -> Coolify deploy` | `Production / GitHub poll -> Coolify deploy` |
| **Command** | `/var/www/scripts/coolify-auto-deploy.sh` | same |
| **Frequency** | `0,5,10,15,20,25,30,35,40,45,50,55 * * * *` | `0,10,20,30,40,50 * * * *` |
| **Timeout (seconds)** | `300` (must be **≥ 60** in Coolify) | same |
| **Container** | staging app container (e.g. `app-staging`) | production app container |

Paste **Frequency** as plain text: exactly **five** cron fields, ASCII spaces only (no smart quotes). The lists above mean “at minutes 0, 5, 10, …” (every 5 or 10 minutes). Coolify’s UI may show **Invalid Cron / Human expression** for some shorthand forms (e.g. `*/5 * * * *`); the explicit minute lists are standard and validate reliably.

Staggering (5 min vs 10 min) reduces the chance of two deploys firing back-to-back when both branches move.

## Templates

Paste-friendly env keys are listed in:

- `docs/deploy-coolify/coolify-staging.env.example`
- `docs/deploy-coolify/coolify-production.env.example`

## Behaviour notes

- The SHA file is updated **after** a successful HTTP response from the deploy endpoint, so a failed request will retry on the next run.
- State files are gitignored under `storage/coolify/`; only the directory is kept in the repo (`.gitkeep`).
