# SDD — Software design document (for AI)

> **Rule for AI:** always update this file and `docs/prd.md` together when there is a relevant change to architecture, flows, rules, or operations.  
> **How these docs relate:** this SDD describes **how the system works**; the PRD describes **what the product must deliver**.

> **Code language:** all source code must be in **English** — identifiers (classes, methods, variables), comments inside code, PHPDoc/DocBlocks, and technical strings in code (e.g. translation keys). User-facing copy may be localized via `lang/` and frontend i18n; the codebase itself stays English-only.

- **Last updated:** 2026-04-21  
- **Purpose:** technical baseline so AI agents can understand the project quickly

## PHP `use` imports (project convention)

When two or more classes come from the **same namespace**, group them on a single `use` statement with brace syntax (compact, matches controllers such as `ItemController`):

```php
use App\Services\Catalog\{CatalogItemViewDataService, ItemService};
use Illuminate\Http\{JsonResponse, RedirectResponse, Request};
```

Single imports from a namespace stay on their own line. Do not mix unrelated namespaces into one grouped `use`.

**Strict types:** PHP sources under `app/`, `routes/`, `config/`, `bootstrap/`, `tests/`, `database/`, and `lang/` declare `strict_types=1` (project-wide baseline). Entry scripts `public/index.php` and `artisan` stay as shipped.

## 0) Public item contribution (`StoreItemContributionAction`) — structure

Read flow top-down; traits live under `app/Actions/Catalog/StoreItemContribution/Concerns/`:

| Piece | Role |
|-------|------|
| `StoreItemContributionRequestContext` | Validates the HTTP request and runs post-success hooks (mail session cleanup). |
| `ResolvesCollaboratorForContribution` | Early exit if collaborator is blocked / unverified / internal. |
| `PreparesContributionItemPayload` | Strips upload-only keys from item payload before persistence. |
| `PersistsContribution` | Opens the DB transaction and delegates completion. |
| `CreatesContributionItem` | Inserts item row, translations, images, identification code, QR best-effort. |
| `CompletesContribution` | Inside the transaction: collaborator name, item + relations (tags, extras, components). |

HTTP redirect and flash success are **not** inside the action; `ItemController::store` applies them after `handle()` succeeds.

## 1) Technical summary

- Project: `e-museu` (Laravel monolith with classic server-rendered web + Vite).
- Core stack: PHP 8.5, Laravel 13, MySQL 8, Redis, Nginx, Node/Vite.
- Default local infra: Docker Compose and the single `./run` script.
- Core domain: collaborative museum-style catalog with admin curation and multi-locale content.

## 2) Architecture and layers

- `app/Http/Controllers`: HTTP layer (request/response).
- `app/Actions`: orchestration for complex use cases.
- `app/Services`: application rules, queries, and view data assembly.
- `app/Models`: Eloquent entities and relationships.
- `app/Support`: cross-cutting helpers (locale fallback, SQL helpers, mail, security).

## 3) Main modules

- Catalog: `Item`, `Extra`, `ItemImage`, `ItemComponent`, `ItemTag`, `ItemCategory`.
- Taxonomy: `Tag`, `TagCategory`, and translations.
- Collaborators: email verification codes and eligibility for public contribution.
- Admin / identity: admin auth, administrative CRUD, and edit locks.
- Admin AI translation: `POST /admin/ai/translate-content` (auth + `throttle:admin-ai-translate`), `config/ai.php`, `App\Support\Admin\Ai\{AdminAi, AdminContentTranslationRegistry, AdminAiViewData, AdminContentTranslationPrompts}`, `App\Services\Ai\*` (OpenRouter client + translation orchestration), admin layout + translation tab partials + `resources/js/pages/admin/ai/admin-content-translation.js`.
- Storage proxy: public files served through an app route (`/storage/{path}`).

## 4) Critical flows (AI-oriented)

- Public item browsing:
  - `GET /catalog/items` — filters, search, sorting.
  - `GET /catalog/items/{id}` — only validated items for the public.
- Public contribution:
  - `POST /catalog/items` → `ItemController@store` → `StoreItemContributionAction`.
  - Pipeline: validate request → resolve collaborator → transaction → create item/translations/images → relations (tags/extras/components) → post-processing.
- Verification:
  - request email code → confirm code → temporary contribution session.
- Admin:
  - CRUD under `/admin/*`, content validation, images/QR management.
  - Optional translation assist: generic JSON endpoint keyed by `resource` (`item`, `extra`, `item_category`, `tag`, `tag_category`) with `translations[locale][field]` echo of the form; modes `fill` vs `regenerate`.

## 5) Rules and invariants

- Public contribution requires a valid email-verification session.
- Internal or blocked collaborators cannot use the public contribution flow.
- Contribution-created content typically starts with `validation=false` (later curation).
- The public should only see validated items.
- Locales and translations use consistent fallback between app logic and SQL.

## 6) Operations and essential commands

- Local setup: `./run setup`
- Start/stop: `./run up` / `./run down`
- Tests: `./run test`
- Quality: `./run phpcs`, `./run phpstan`, `./run all-tests`
- Local dev and CI assume MySQL.

## 7) Known technical risks

- Tight coupling in central catalog classes.
- MySQL-specific SQL (limited portability).
- Contribution flow relies on session state.
- External QR generation can add latency or intermittent failures.

## 8) How AI should use this file

- Read this SDD before proposing structural refactors.
- Cross-check `docs/prd.md` to ensure technical changes still match product intent.
- Update **Last updated** and **Change log** whenever rules or flows change.

## Change log (short)

- **2026-04-21**: Document renamed from `spp.md` (typo) to **`sdd.md`** (software design document). Added **PHP `use` imports** convention. English-only code rule. Section “Public item contribution (`StoreItemContributionAction`) — structure” (traits table). README deploy paths → `docs/deploy/`. Stricter rate limit on `catalog.items.store`; unknown contribution status → validation error (not HTTP 500).
- **2026-04-21 (later):** `declare(strict_types=1)` applied across `app/`, `routes/`, `config/`, `bootstrap/`, `tests/`, `database/`, `lang/`. Public contribution components must reference **validated** items only (`ComponentRequest` + `ItemComponentService`). Admin partial edit actions: lock semantics in `docs/internal/edit-locks.md`.
- **2026-04-21 (later still):** `config/ai.php`, `RegistersRateLimiters::admin-ai-translate`, `AdminContentTranslationController` + `AdminContentTranslationRequest`, OpenRouter-backed `AdminContentTranslationService`, feature tests `AdminContentTranslationControllerTest`; admin AI support classes live under `App\Support\Admin\Ai` (registry, prompts, layout flags, etc.).
