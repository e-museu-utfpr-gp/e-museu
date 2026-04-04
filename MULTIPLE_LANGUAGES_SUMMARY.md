
Listed **oldest → newest** (reverse of default `git log` order).

### 1. Admin language switch

- **`SetLocaleFromSession`**: UI locale validation no longer uses a hardcoded enum list; it delegates to **`Language::isValidSessionUiLocale()`** so allowed session locales stay aligned with configured languages.
- **`Language` model**: initial support used by the middleware and provider (e.g. session locale validation).
- **`AppServiceProvider`**: registers / shares language-related configuration for the app.
- **Admin layout copy**: `lang/en/view/admin/layout.php`, `lang/pt_BR/view/admin/layout.php`.
- **`resources/views/components/layouts/admin.blade.php`**: admin language switcher UI.
- **`routes/web.php`**: small routing adjustments tied to admin / locale behavior.

### 2. Public language switch

- **`Language` model**: additions for the public locale switcher (e.g. listing choices for the UI).
- **`AppServiceProvider`**: further wiring for public vs admin locale behavior.
- **`lang/en/view/layout.php`**, **`lang/pt_BR/view/layout.php`**: strings for the public language control.
- **`phpstan.neon`**: adds an **ignore pattern** for PHPStan on **`Language::forLocaleSwitcher()`** return type (not a functional runtime change).
- **`resources/sass/custom.scss`**: styles for the public language switcher.
- **`resources/views/components/layouts/app.blade.php`**: public site language selector and layout integration.
- **`resources/views/components/layouts/admin.blade.php`**: minor tweaks alongside the public layout work.

### 3. Admin item edition with several languages

- **`AdminItemController`**: create/update flows handle per-locale item payload.
- **`AdminItemTranslationsRules`**: new validation rules for item translations (~111 lines added in that commit).
- **`AdminStoreItemRequest`**, **`AdminUpdateItemRequest`**: integrated with translation validation.
- **`Item` model**, **`ItemService`**: load/save translated fields across languages.
- **`Language`**: small follow-ups for item forms.
- **`lang/*/validation.php`**, **`lang/*/view/admin/catalog.php`**: messages and admin catalog strings for translations.
- **Blade**: new **`items/_partials/translation-tabs.blade.php`**; admin item **`create.blade.php`** / **`edit.blade.php`** refactored around tabbed per-language fields.

---

## Uncommitted work (working tree vs `HEAD`)

Snapshot: **`git diff --stat HEAD`** reports **95 files changed**, **1674 insertions(+), 686 deletions(-)** (plus **untracked** files listed below). Grouped by theme.

### Configuration and documentation

- **`.env.example`**, **`README.md`**: environment examples and documentation for multilingual / content-locale behavior.
- **`phpunit.xml`**: test configuration adjustments.
- **`vite.config.js`**: build config updates (bundling / inputs related to frontend changes).

### Content locale and HTTP helpers

- **`ContentLanguage` enum**: clarified or extended for content vs UI locale and form defaults.
- **`OptionalContentLocale`** (`app/Support/Http/OptionalContentLocale.php`): reads optional **`content_locale`** from the request (query/body); empty means “use site fallback”; non-empty must match **`languages.code`**, otherwise throws **`ValidationException`** with `content_locale` errors.

### Database

- Migrations adjusted: **`languages`**, **`tag_categories`**, **`tag_category_translations`**.
- **`ItemCategorySeeder`**, **`ItemFactory`**: aligned with translation data and relations.

### Models and shared concerns

- **`Item`**, **`ItemTranslation`**, **`ItemComponent`**, **`ItemTag`**, **`Extra`**, **`ItemCategory`**, **`Tag`**, **`TagCategory`**, **`Collaborator`**: translation relations, accessors, or slimmer models (e.g. categories/tags delegating shared behavior).
- **`Lock`** and **`LockService`**: **per-request cache** for **`findByModel()`** (invalidated on lock save/delete), use of **`getKey()`** for the lockable id, and **`requireUnlockedThenLock()`** so **`requireUnlocked()`** + **`lock()`** do not each query the DB separately — performance/correctness for admin edit flows (same branch as multilingual admin work, not content-locale logic per se).
- **`Language`**: further methods and integration beyond the last committed state.

**New trait:** `app/Models/Concerns/SyncsAdminFormNameTranslations.php` — **`syncTranslationsFromAdminForm()`** and related helpers for models whose main translatable admin field is **`name`** (item categories, tag categories, tags), including empty-name cleanup via deleting translation rows.

### Admin HTTP layer

- Controllers updated: **extras**, **item categories**, **item tags**, **items**, **collaborators**, **tag categories**, **tags** — load/save translations, pass locale context, use new request rules.
- **Requests**
  - **`AdminItemCategoryRequest`**, **`AdminStoreExtraRequest`**, **`AdminTagCategoryRequest`**, **`AdminItemTranslationsRules`**: multi-locale validation and structure (rules files evolved after `bfb7316`).
  - **Removed (tracked deletion):** `AdminSingleTagRequest.php`.
  - **New (untracked until committed):** `AdminExtraTranslationsRules.php`, `AdminItemCategoryTranslationsRules.php`, `AdminTagCategoryTranslationsRules.php`, `AdminTagTranslationsRules.php`, `AdminTagRequest.php`.
- **Support**
  - **`AdminEditHeadingLocale.php`**: admin edit headings / locale display helpers.
  - **`AdminTranslatableNameFormRules.php`**: shared validation for translatable `name` fields in admin forms.

### Catalog services and queries

- **`ItemService`**, **`ExtraService`**, **`ItemCategoryService`**, **`ItemTagService`**, **`TagService`**, **`TagCategoryService`**: persist and resolve translated attributes on create/update/show.
- **`ItemIndexQueryBuilder`**, **`TranslationDisplaySql`**: listing/display SQL for translation resolution.
- **`StoreItemContributionAction`**, **`ItemContributionValidator`**, **`StoreItemRequest`**, **`ComponentRequest`**, **`SingleExtraRequest`**: contribution and public item flows respect content locale where applicable.
- **`ContributionContentLocaleService`**: builds **contribution/extra form** options (`contributionLanguages`, `defaultContentLocale`) using **`Language::forAdminContentForms()`** and **`ContentLocaleFallback`**; **not** responsible for persisting the item itself (per class docblock).

### Public catalog and routes

- **`ItemController`**, **`TagController`**, **`CollaboratorController`**: resolved translations and optional **`content_locale`** on JSON/list endpoints where implemented.
- **`routes/web.php`**: route or middleware adjustments for locale-related endpoints.
- **Catalog views**: item show/create, filters, sidebars, extra modal, timelines — translated strings and locale-aware behavior.
- **Admin views**: extras, categories, tags, tag categories, items, collaborators — translation tabs and shared partials.
- **`language-tab-label.blade.php`**, **`info-popover.blade.php`**, **`custom.scss`**, **`admin.blade.php`**: shared UI for tabs/popovers/layout.

### Identity / admin services

- **`AdminService`**: small updates alongside admin/locale behavior.

### Frontend (JavaScript)

- **`i18n.js`**: client-side locale / message handling updates.
- **`section-item-selector.js`**, **`check-contact.js`**, **`component-modal.js`**, **`tag-modal.js`**: payloads or UX aligned with multilingual catalog data.
- **`popover-button.js`**: larger update (interaction / positioning / integration with new UI).

### Tests

- **Updated:** `TagFindOrCreateTest`, `TranslationResolutionTest`, `ContentLocaleFallbackTest`.
- **New (untracked):** `OptionalContentLocaleJsonTest` — asserts **422** + JSON validation errors for invalid **`content_locale`** on **`catalog.items.byCategory`** and **`catalog.tags.autocomplete`**.
