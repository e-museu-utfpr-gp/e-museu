# Coolify deploy hook (`POST /deploy`)

GitHub Actions calls the public Laravel URL **`POST /deploy`** on push to **`main`** (production) or **`develop`** (staging). The app verifies an **`Authorization: Bearer`** secret, then performs an authenticated **HTTP GET** to the Coolify deploy API URL for **that** Coolify resource (different UUID per service). Nginx only allows **POST** on `/deploy`.

### Why the application triggers its own deploy

From outside the project it can look odd that **Laravel calls Coolify to redeploy the stack that runs Laravel**. That is intentional here.

At **UTFPR**, management blocks **Coolify’s HTTP API port (typically 8000)** and other non-standard ports from the public internet for institutional security. GitHub Actions runners therefore **cannot** call Coolify directly. What **is** reachable over HTTPS are the usual web fronts (**production** and **staging**). The safest workable pattern is: **GitHub → public app URL (`POST /deploy`) → server-side call to Coolify on the internal network**. The hook adds **Bearer authentication**, **strict rate limiting** (5 requests/minute per IP), **POST-only** handling at Nginx, and **no CSRF** on this single route—reducing exposure compared with opening Coolify to the world.

Legacy **`scripts/coolify-auto-deploy.sh`** remains available for optional scheduled polling (configure **`COOLIFY_AUTO_DEPLOY_*`** in Coolify if you use it; those keys are not in the paste-friendly `.env.example` templates). Primary automation is this webhook plus `.github/workflows/deploy-coolify.yml`.

## Application environment (per Coolify resource)

| Variable | Description |
|----------|-------------|
| `DEPLOY_HOOK_SECRET` | Same value on **production and staging** Coolify apps; must match GitHub secret **`DEPLOY_HOOK_BEARER`** (`Authorization: Bearer` on hook requests). |
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

Create these under the **Variables** tab (`vars.DEPLOY_HOOK_URL_*` in the workflow). **Do not** put hook URLs in **Secrets** — the workflow reads **`vars.*`** only; if you save URLs as secrets, **`vars` stays empty** and `curl` fails with a malformed/empty URL.

Paste-friendly reference (same `KEY=value` layout as Coolify templates): `docs/deploy-coolify/github-actions-deploy.env.example` — copy **`DEPLOY_HOOK_URL_*`** into **Variables**; copy **`DEPLOY_HOOK_BEARER`** into **Secrets**.

Staging **`STAGING_HTTP_USER` / `STAGING_HTTP_PASSWORD`** still protect normal browser traffic; **`POST /deploy` skips that Basic Auth** so GitHub Actions can send **`Authorization: Bearer`** only (HTTP allows one `Authorization` scheme per request).

### Repository secrets

| Secret | Used when |
|--------|-----------|
| `DEPLOY_HOOK_BEARER` | Every deploy trigger; must equal **`DEPLOY_HOOK_SECRET`** on **both** production and staging Laravel/Coolify env. |

Workflow file: `.github/workflows/deploy-coolify.yml`.

## Nginx

`docker/nginx/*/default.conf` defines `location = /deploy` with `limit_except POST { deny all; }` and forwards to Laravel via `try_files` → `index.php`.

## Operations

- Disable Coolify **Scheduled Tasks** that ran `coolify-auto-deploy.sh` if you rely solely on Actions, to avoid duplicate deploy triggers.
- Confirm the app container can reach `COOLIFY_DEPLOY_URL` (internal host/IP and port **8000** if applicable).

### Browser **GET** vs CI **POST**

- **GET** `https://…/deploy` in a browser hits Nginx **`limit_except POST`** → **403** from Nginx (expected; the hook is **POST** only).
- **POST** with **`Authorization: Bearer`** must reach Laravel; if you still see **401**, read below.

### GitHub Actions gets **401 Unauthorized**

Typical causes:

1. **Staging HTTP Basic Auth** (`STAGING_HTTP_USER` / `STAGING_HTTP_PASSWORD`) — **`StagingBasicAuth`** returns **401** with **`WWW-Authenticate: Basic`** when credentials are missing or wrong. The hook must **skip** that middleware for path **`/deploy`** (implementation uses **`getPathInfo()`** so it always matches). Deploy the latest app image to staging, then re-run Actions. Until then: deploy once from Coolify UI, or test locally with `curl -u user:pass -H "Authorization: Bearer …" -X POST …/deploy`.
2. **Cloudflare (or another edge)** — **Zero Trust / Access**, **WAF**, or an **Institutional proxy** may return **401** before the request reaches Laravel. Check the response body and headers in the Actions log (`curl -v` temporarily in a branch) or Cloudflare **Security → Events**. Add a bypass or service token for **`POST /deploy`** if needed.
3. **Wrong URL** — ensure the variable points to **`https://…/deploy`** (no typo, no extra path).

### **403 Forbidden** on `POST /deploy`

Laravel returns **403** when **`Authorization: Bearer …`** does not match **`DEPLOY_HOOK_SECRET`** (after trim), or when **`DEPLOY_HOOK_SECRET`** is empty/missing in the running container.

Checklist:

- **GitHub** secret **`DEPLOY_HOOK_BEARER`** and **Coolify** **`DEPLOY_HOOK_SECRET`** for **that** environment (staging vs production) must be the **same string** (no extra quotes in the value; paste can add spaces or newlines — app and workflow now trim).
- After changing env in Coolify, **redeploy** or run **`php artisan config:clear`** inside the app container so `config('deploy.secret')` picks up the new value (if config was cached).
- Confirm you are hitting the **correct** URL (staging variable vs production) so the secret you set in Coolify matches the app that receives the request.

The Laravel route uses throttle **`deploy-hook`**: **5 requests per minute per IP**.

## Change log (short)

- **2026-05-06:** Replaced GitHub SHA polling docs with deploy-hook + Actions workflow; env keys `DEPLOY_HOOK_SECRET`, `COOLIFY_DEPLOY_URL`, `COOLIFY_DEPLOY_TOKEN`.
- **2026-05-06:** Documented rationale (UTFPR firewall / blocked Coolify port); deploy-hook rate limit **5/min** per IP.
- **2026-05-06:** Deploy hook URLs moved to GitHub repository **variables** `DEPLOY_HOOK_URL_PRODUCTION` / `DEPLOY_HOOK_URL_STAGING`.
- **2026-05-06:** Single GitHub secret **`DEPLOY_HOOK_BEARER`** for both environments; **`DEPLOY_HOOK_SECRET`** must match on prod and staging.
- **2026-05-06:** Workflow trims hook URLs and requires **`https://`**; **`StagingBasicAuth`** skips **`/deploy`** (Bearer-only hook); removed **`STAGING_BASIC_AUTH_CURL`** from Actions (URLs must live under **Variables**, not **Secrets**, for `vars.*`).
- **2026-05-06:** **`StagingBasicAuth`** skips hook via **`getPathInfo()`** (`/deploy` prefix); doc clarifies **GET → 403** (Nginx) vs **POST + 401** (Basic Auth if bypass missing).
- **2026-05-06:** Hook **403** checklist; **`trim`** on Bearer/`DEPLOY_HOOK_SECRET`; Actions trims bearer and prints hints on **403**/**503**.
