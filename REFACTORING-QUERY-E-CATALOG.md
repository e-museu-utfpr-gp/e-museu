# Refactoring: Query, Filters & Public Catalog

Summary of changes to pagination, filters, and catalog controller organization.

---

## 1. QueryController removed

- **Deleted:** `app/Http/Controllers/Catalog/QueryController.php` (non-semantic, scattered responsibilities).
- **Redistribution:** Tag endpoints → **TagController** (new), delegating to `TagService` (`index`, `autocomplete`, `checkName`). Component autocomplete/check → **ItemController** via `ItemService`. Contact check → **CollaboratorController** (new) via `CollaboratorService`.

## 2. Routes

- New/updated in `routes/web.php`: `GET /tags`, `/tags/autocomplete`, `/tags/check-name`; `/items/component-autocomplete`, `/items/check-component-name`; `/collaborators/check-contact`. Specific `items/*` routes placed **before** `items/{id}` to avoid conflicts.

## 3. Controllers → Services

- Controllers do not touch models directly; all data access goes through **Services** (and Actions where used). TagController, CollaboratorController, and ItemController inject and delegate to the corresponding services.

## 4. Support: Query builders

- **AdminIndexQueryBuilder** (`App\Support`): single `build(Builder, Request, array $config): void` — applies search + sort for admin index.
- **ItemIndexQueryBuilder** (`App\Support`): `build(Request): Builder` — builds public catalog query (validated items, filters, sort). Caller paginates.

## 5. Catalog index in Service

- Public item index logic moved from ItemController to **ItemService::getPaginatedItemsForCatalogIndex(Request)**. Uses `ItemIndexQueryBuilder::build()`, paginates (24), `withQueryString()`, `appends(['item_category','order'])`, resolves category name; returns `['items','categoryName']`. Naming aligned with admin: `getPaginatedItemsForCatalogIndex` / `getPaginatedItemsForAdminIndex`.

## 6. Filter & sort (catalog)

- Filter form: hidden input `order` with `request()->query('order', 1)` so sort is preserved when applying other filters. Paginator appends `order` and `item_category` so pagination links keep filters and sort.

## 7. Seeder / ItemTagFactory

- Public catalog requires `item_tag.validation = true`. **ItemTagFactory** now sets `'validation' => true` so seeded item–tag links show up when filtering by tag category.

## 8. Files touched

| Type       | Files |
|-----------|--------|
| Removed   | `QueryController.php` |
| Controllers | `ItemController`, `TagController`, `CollaboratorController` |
| Services  | `ItemService`, `TagService`, `CollaboratorService` |
| Support   | `AdminIndexQueryBuilder`, `ItemIndexQueryBuilder` |
| Other     | `routes/web.php`, catalog views/filter menu, modals, JS, `ItemTagFactory` |
