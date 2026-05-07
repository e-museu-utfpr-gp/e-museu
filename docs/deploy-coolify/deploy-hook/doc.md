# Coolify deploy hook (`POST /deploy`)

GitHub Actions calls the public Laravel URL **`POST /deploy`** on push to **`main`** (production) or **`develop`** (staging). The app verifies an **`Authorization: Bearer`** secret, then performs an authenticated **HTTP GET** to the Coolify deploy API URL for **that** Coolify resource (different UUID per service). Nginx only allows **POST** on `/deploy`.

### Why the application triggers its own deploy

From outside the project it can look odd that **Laravel calls Coolify to redeploy the stack that runs Laravel**. That is intentional here.

At **UTFPR**, management blocks **Coolify’s HTTP API port (typically 8000)** and other non-standard ports from the public internet for institutional security. GitHub Actions runners therefore **cannot** call Coolify directly. What **is** reachable over HTTPS are the usual web fronts (**production** and **staging**). The safest workable pattern is: **GitHub → public app URL (`POST /deploy`) → server-side call to Coolify on the internal network**. The hook adds **Bearer authentication**, **strict rate limiting** (5 requests/minute per IP), **POST-only** handling at Nginx, and **no CSRF** on this single route—reducing exposure compared with opening Coolify to the world.

Legacy **`scripts/coolify-auto-deploy.sh`** remains available for optional scheduled polling (configure **`COOLIFY_AUTO_DEPLOY_*`** in Coolify if you use it; those keys are not in the paste-friendly `.env.example` templates). Primary automation is this webhook plus `.github/workflows/deploy-coolify.yml`.

## Application environment (per Coolify resource)

| Variable | Description |
|----------|-------------|
| `DEPLOY_HOOK_SECRET` | Shared with GitHub (`DEPLOY_HOOK_BEARER_MAIN` / `DEPLOY_HOOK_BEARER_DEVELOP`); must match `Authorization: Bearer` on incoming hook requests. |
| `COOLIFY_DEPLOY_URL` | Full Coolify deploy API URL (`…/api/v1/deploy?uuid=…&force=false`). Quote in Coolify YAML if it contains `&`. |
| `COOLIFY_DEPLOY_TOKEN` | Coolify API token with deploy permission (`Authorization: Bearer` on the outbound GET). |

If `DEPLOY_HOOK_SECRET` or Coolify settings are missing or empty, the hook returns **403** or **503**.

## GitHub Actions configuration

Repository **Settings → Secrets and variables → Actions**.

### Repository variables (public URLs; not masked in logs like secrets)

| Variable | Example | Used when |
|----------|---------|-----------|
| `DEPLOY_HOOK_URL_PRODUCTION` | `https://e-museu.gp.utfpr.edu.br/deploy` | Push to `main`. |
| `DEPLOY_HOOK_URL_STAGING` | `https://staging.e-museu.gp.utfpr.edu.br/deploy` | Push to `develop`. |

Create these under the **Variables** tab (`vars.DEPLOY_HOOK_URL_*` in the workflow).

Paste-friendly reference (same `KEY=value` layout as Coolify templates): `docs/deploy-coolify/github-actions-deploy.env.example` — copy **`DEPLOY_HOOK_URL_*`** into **Variables**; copy **`DEPLOY_HOOK_BEARER_*`** and optional **`STAGING_BASIC_AUTH_CURL`** into **Secrets**.

### Repository secrets

| Secret | Used when |
|--------|-----------|
| `DEPLOY_HOOK_BEARER_MAIN` | Push to `main`; must equal production `DEPLOY_HOOK_SECRET`. |
| `DEPLOY_HOOK_BEARER_DEVELOP` | Push to `develop`; must equal staging `DEPLOY_HOOK_SECRET`. |
| `STAGING_BASIC_AUTH_CURL` | Optional; push to `develop` only. If staging uses `STAGING_HTTP_USER` / `STAGING_HTTP_PASSWORD`, set this to **`user:password`** for `curl -u` (same pair as the app). Leave unset if staging basic auth is disabled. |

Workflow file: `.github/workflows/deploy-coolify.yml` (production step has no basic auth; staging passes `-u` when `STAGING_BASIC_AUTH_CURL` is set).

## Nginx

`docker/nginx/*/default.conf` defines `location = /deploy` with `limit_except POST { deny all; }` and forwards to Laravel via `try_files` → `index.php`.

## Operations

- Disable Coolify **Scheduled Tasks** that ran `coolify-auto-deploy.sh` if you rely solely on Actions, to avoid duplicate deploy triggers.
- Confirm the app container can reach `COOLIFY_DEPLOY_URL` (internal host/IP and port **8000** if applicable).

The Laravel route uses throttle **`deploy-hook`**: **5 requests per minute per IP**.

## Change log (short)

- **2026-05-06:** Replaced GitHub SHA polling docs with deploy-hook + Actions workflow; env keys `DEPLOY_HOOK_SECRET`, `COOLIFY_DEPLOY_URL`, `COOLIFY_DEPLOY_TOKEN`.
- **2026-05-06:** Documented rationale (UTFPR firewall / blocked Coolify port); deploy-hook rate limit **5/min** per IP.
- **2026-05-06:** Deploy hook URLs moved to GitHub repository **variables** `DEPLOY_HOOK_URL_PRODUCTION` / `DEPLOY_HOOK_URL_STAGING`.
