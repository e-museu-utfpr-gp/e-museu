## Goals

- Let **external collaborators** prove they control an e-mail address **before** submitting public catalog contributions (items / extras).
- Send **transactional e-mails** when a contribution is received, when the stack is actually able to send mail.
- Avoid leaking implementation details in **production** when mail is missing or sending fails; **rate-limit** abuse-prone endpoints.
- Run **sessions and application cache on Redis** in real environments so verification state and throttle counters behave consistently across processes and deploys (see [Redis](#redis)).

---

## Public e-mail verification flow

- **Six-digit code**, **15-minute TTL**, stored in **session** as a **hash** (plain code only in the outgoing message). Hashing ties to `config('app.key')` (key rotation invalidates in-flight codes).
- **Endpoints** (under URL prefix `/catalog/…`, JSON), with throttling. Registered **route names** include the `catalog.` prefix:
  - `POST catalog/collaborators/request-verification-code` → `catalog.collaborators.request-verification-code` (`throttle:collaborator-verification-email`).
  - `POST catalog/collaborators/confirm-verification-code` → `catalog.collaborators.confirm-verification-code` (`throttle:collaborator-verification-confirm`).
  - `POST catalog/collaborators/clear-contribution-session` → `catalog.collaborators.clear-contribution-session` (`throttle:collaborator-clear-session`).
- **Contact lookup (shared controller):** `POST catalog/collaborators/check-contact` → `catalog.collaborators.check-contact` (`throttle:collaborator-check-contact`). Admin uses the same `checkContact` action with route `admin.catalog.collaborators.check-contact` and `throttle:web-admin` (see `CollaboratorController` docblock).
- **Service:** `App\Services\Collaborator\CatalogCollaboratorVerificationService` — handles send/confirm, internal-vs-external rules, blocked collaborators, and session cleanup on failure.
- **Mail:** `App\Mail\CollaboratorVerificationCodeMail` + `resources/views/emails/collaborator-verification-code.blade.php`; copy in `lang/*/mail.php`.
- **Readiness:** No send is attempted unless `App\Support\Mail\OutgoingMailIsConfigured::forDefaultMailer()` is true.
- **Errors:** `App\Support\Catalog\CatalogVerifyMailError` uses a **generic** user message (`app.collaborator.verify_service_unavailable`) when `APP_ENV=production` and `APP_DEBUG=false` (still logs server-side); otherwise uses `app.collaborator.verify_mail_not_configured` or `app.collaborator.verify_mail_send_failed`.
- **UI:** Partial `resources/views/pages/catalog/items/_partials/email-verification-code.blade.php` (included from item create and extra modal). JS: `resources/js/pages/catalog/collaborators/email-verification-code.js`, `resources/js/shared/catalog/clear-contribution-session.js`, plus `extra-modal-clear-session-on-hide.js` and hooks from `clear-item-create-form.js` / `app.js` (`data-route-clear-contribution-session`).

---

## Contribution gating and session auth

- Collaborators expose `last_email_verification_at` and `hasVerifiedEmail()`; the public contribution path requires verification **and** a **session-authenticated** match for that e-mail (`CollaboratorService`).
- Failed or incomplete verification surfaces as structured outcomes; `App\Support\Catalog\PublicCatalogContributionOutcome` maps statuses (e.g. `email_unverified`, blocked, internal reserved) to validation errors for the public forms.
- **Request layer:** `PublicCollaboratorRules` and updates to catalog validators/controllers/services ensure the server enforces the same rules as the UI.

---

## “Contribution received” e-mails

- **Actions:**  
  - `SendItemContributionReceivedMailAction` / `SendExtraContributionReceivedMailAction` — send only if mail is configured; load collaborator and localized display name where applicable.  
  - `CompleteCatalogItemContributionAction` / `CompleteCatalogExtraContributionAction` — **best-effort** send, log transport failures, then **always** clear public contribution session auth so users still get success and the session is not left half-open.
- **Mailables:** `ItemContributionReceivedMail`, `ExtraContributionReceivedMail` with matching Blade templates under `resources/views/emails/`. The extra “received” mail uses the **parent item** display name (same untitled fallback key as the item mail), not a separate extra title field.

---

## Admin and data model

- **Migration / model:** `last_email_verification_at` on `collaborators` (nullable timestamp).
- **Admin UI:** Create/edit collaborator forms include optional “e-mail confirmed at”; index/show reflect verification state; `mark-email-verified-at-now.js` helps set “now” from the browser.
- **Admin validation:** `AdminCollaboratorRules` (and related requests) allow the new field; `CollaboratorService` handles create/update semantics (e.g. clearing verification when e-mail changes unless a new timestamp is set, auto-verify internal where appropriate).
- **Listing:** Admin index config/table supports search/sort by `last_email_verification_at`.

---

## Mail configuration and infrastructure

- **`config/mail.php`:** Extends configuration so each transport can declare **required config keys** (`transport_required_config`); `OutgoingMailIsConfigured` implements SMTP, API transports, failover/round-robin (with stricter behaviour in production so “log-only” stacks do not count as deliverable).
- **`.env.example` / README:** Document variables needed for real outbound mail in local/CI/Docker contexts.
- **Docker / compose:** Images and compose files adjusted so mail-related env can be passed consistently across local, staging, and production-oriented setups.
- **`AppServiceProvider`:** Calls `App\Providers\Concerns\RegistersRateLimiters::register()` (includes existing limiters plus collaborator verification, check-contact, clear-session, admin login keying). `bootstrap/app.php` may still change for other app wiring; rate limiter definitions live in `RegistersRateLimiters`.
- **CI:** Workflow and scripts updated (e.g. compose preparation for tests) so the pipeline matches the new services/config.
- **`deploy/`:** Coolify-oriented env templates: `coolify-production.env.example`, `coolify-staging.env.example`.

---

## Redis

Redis is part of this work so the stack matches how **sessions**, **cache**, and **HTTP rate limits** behave in production—especially important for the public verification flow (pending code + session auth live in the session store; throttles use the cache store).

### Configuration

- **`config/database.php`:** Two logical connections, `redis.default` and `redis.cache`, backed by `REDIS_DB` and `REDIS_CACHE_DB` (separate Redis keyspaces). Supports **`REDIS_URL`** (parsed by Laravel) or discrete `REDIS_HOST` / `REDIS_PORT` / `REDIS_PASSWORD` / `REDIS_USERNAME`. Client default: **`phpredis`** (`REDIS_CLIENT`).
- **`config/session.php`:** Default driver from env — **`SESSION_DRIVER=redis`** in `.env.example` (pending verification payload and public contribution session flags are session-backed).
- **`config/cache.php`:** Default store from env — **`CACHE_DRIVER=redis`** in `.env.example`. Laravel’s **rate limiter** uses the application cache; custom limiters in `RegistersRateLimiters` therefore hit Redis when `CACHE_DRIVER=redis`.
- **`.env.example`:** Documents the above and explicitly notes that with **`redis`** as the session driver, **pending e-mail verification data is stored in Redis** (not only in the PHP process).

### Docker / hosting

- **Local / prod-local / production-test compose:** A **`redis:7.4-alpine`** service is defined; the app service **`depends_on: redis`** and uses host `redis` on port `6379` (e.g. `REDIS_URL=redis://redis:6379` where set in compose).
- **Staging & production Coolify compose** (`docker-compose.staging.yml`, `docker-compose.production.yml`): **no bundled Redis container** — Redis is **external**; `deploy/coolify-*.env.example` includes **`REDIS_URL=redis://YOUR_REDIS_HOST:6379/0`** as the integration hint.

### Tests

- **`phpunit.xml`** forces **`SESSION_DRIVER=array`** (and related testing env) so PHPUnit does **not** require a live Redis instance for the default test run—unlike `.env.example` defaults for real deployments.

---

## Tests

- **Feature (examples tied to this work):**  
  `tests/Feature/Catalog/CollaboratorEmailVerificationTest.php`,  
  `tests/Feature/Catalog/CatalogVerifyMailErrorTest.php`,  
  `tests/Feature/Admin/AdminCollaboratorCheckContactRouteTest.php`,  
  `tests/Feature/Catalog/ItemContributionStoreDateTest.php` (contribution + verification/session assumptions),  
  `tests/Feature/Collaborator/CollaboratorServiceManualEmailVerificationTest.php`.
- **Unit:**  
  `tests/Unit/Support/Mail/OutgoingMailIsConfiguredTest.php`,  
  `tests/Unit/Support/Catalog/PublicCatalogContributionOutcomeTest.php`.
- **`phpunit.xml`:** Sets `DB_DATABASE=emuseu_testing`, `MAIL_MAILER=array` (so tests avoid real SMTP/API while `OutgoingMailIsConfigured` still accepts the array driver), `SESSION_DRIVER=array`, and documents the `#[Group('mysql')]` feature tests for CI/MySQL.

---

## Other touched areas (supporting the feature)

- **Routes:** `routes/web.php` — new catalog JSON routes and middleware.
- **Catalog controllers** (`ItemController`, `ExtraController`, `CollaboratorController`) — wire verification, completion actions, and outcomes.
- **Languages:** `lang/en` and `lang/pt_BR` — app, validation, view, and **mail** strings for verification and receipt e-mails.
- **Front-end:** `resources/js/app.js` / `admin.js` entries for new pages; `check-contact.js` and contribution form scripts aligned with verification and session clearing.
- **Misc:** `composer.json` / `composer.lock` if new packages; `eslint.config.js`, `custom.scss`, and small UI component tweaks where forms gained fields.

---

## Untracked / local artifacts to be aware of

- `emuseu_dump.sql` — database dump; typically **not** committed to the application repo (confirm team policy).
- `deploy/` — review whether all files under it should be versioned.

---

## Quick file map (core additions)

| Area | Paths |
|------|--------|
| Verification service | `app/Services/Collaborator/CatalogCollaboratorVerificationService.php` |
| Mail readiness | `app/Support/Mail/OutgoingMailIsConfigured.php`, `app/Support/Mail/EmailVerificationCode.php` |
| Mailables | `app/Mail/CollaboratorVerificationCodeMail.php`, `ItemContributionReceivedMail.php`, `ExtraContributionReceivedMail.php` + `resources/views/emails/*.blade.php` |
| Completion / send actions | `CompleteCatalogItemContributionAction.php`, `CompleteCatalogExtraContributionAction.php`, `SendItemContributionReceivedMailAction.php`, `SendExtraContributionReceivedMailAction.php` (all under `app/Actions/Catalog/`) |
| HTTP / support | `app/Support/Catalog/CatalogVerifyMailError.php`, `PublicCatalogContributionOutcome.php` |
| Public rules | `app/Http/Requests/Collaborator/PublicCollaboratorRules.php` |
| Rate limits | `app/Providers/Concerns/RegistersRateLimiters.php` |
| Redis / session / cache | `config/database.php` (`redis` connections), `config/session.php`, `config/cache.php`, `.env.example` |
