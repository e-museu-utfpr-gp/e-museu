# E-Museu Project Refactoring

Documentation of table, model, controller, route, and related component naming changes performed in the project.

---

## 1. Summary of changes

| Before | After |
|--------|--------|
| Table `users` | Table `admins` |
| Table `categories` (taxonomy) | Table `tag_categories` |
| Table `sections` (catalog) | Table `item_categories` |
| Table `proprietaries` | Table `collaborators` |
| Table `tag_item` | Table `item_tag` |
| Column `is_admin` on users/admins | **Removed** |
| Column `is_admin` on proprietaries/collaborators | Replaced by enum `CollaboratorRole` (internal / external) |
| Admin resource route `admin/users` | `admin/admins` |
| Components resource route `admin/components` | `admin/item-components` |
| Model `TagItem` | Model `ItemTag` |

---

## 2. Authentication and administrators

### 2.1 Database
- **Table:** `users` → `admins`
- **Removed column:** `is_admin` (all records are administrators)
- **Table `locks`:** column `user_id` → `admin_id` (FK to `admins.id`)

### 2.2 Model
- **Class:** `App\Models\Identity\User` → `App\Models\Identity\Admin`
- **File:** `app/Models/Identity/Admin.php`
- **Table:** `admins`
- **Relation:** `locks()` using `admin_id`

### 2.3 Controller, routes, and views
- **Controller:** `AdminUserController` → `AdminController` (`App\Http\Controllers\Admin\Identity\AdminController`)
- **Route:** `admin/users` → `admin/admins` (resource and delete-lock)
- **Route names:** `admin.users.*` → `admin.admins.*`
- **Request:** `UserRequest` → `AdminRequest`
- **Views:** `admin/identity/users` → `admin/identity/admins` (index, show, create)
- **Lang keys:** `view.admin.identity.users` → `view.admin.identity.admins`, `app.identity.user` → `app.identity.admin`

### 2.4 Auth config (`config/auth.php`)
- **Defaults passwords:** `users` → `admins`
- **Guards web provider:** `users` → `admins`
- **Providers:** key `users` → `admins`, model `App\Models\Identity\Admin::class`
- **Passwords:** key `users` → `admins`, provider `admins`

### 2.5 Sessions
- **Table `sessions`:** column `user_id` → `admin_id` (FK to `admins.id`)
- **Custom handler:** `App\Providers\Concerns\AdminDatabaseSessionHandler` extends Laravel’s `DatabaseSessionHandler` and writes `admin_id` instead of `user_id` in the session payload
- **Registration:** `Session::extend('database', ...)` in `AppServiceProvider` so that with `SESSION_DRIVER=database` the app uses the admin_id handler
- **Migration:** `2026_03_01_000010_create_sessions_table.php` creates `sessions` with `admin_id` (and `ip_address`, `user_agent`, `payload`, `last_activity`)

### 2.6 Other
- **Lock model:** `admin_id`, relation `admin()`
- **LocksSubject, ReleaseLockController:** use `admin_id` and logged-in admin
- **CreateAdmin command:** uses `Admin`, no `is_admin`
- **UserSeeder** → **AdminSeeder** (`Database\Seeders\Identity\AdminSeeder`), uses `Admin`
- **Layout admin:** link to `admin.admins.index`
- **JS:** delete confirmation uses `.deleteAdminButton` and `warnings.delete_admin`
- **Removed:** `AdminUserController`, `UserRequest`, `admin/identity/users` views, `User` model (Identity)

---

## 3. Tag categories (taxonomy)

### 3.1 Database
- **Table:** `categories` → `tag_categories`
- **Table `tags`:** column `category_id` → `tag_category_id` (FK to `tag_categories.id`)

### 3.2 Model
- **Class:** `App\Models\Taxonomy\Category` → `App\Models\Taxonomy\TagCategory`
- **File:** `app/Models/Taxonomy/TagCategory.php`
- **Table:** `tag_categories`
- **Relation in Tag:** `category()` → `tagCategory()`, FK `tag_category_id`

### 3.3 Controller and routes
- **Controller:** `AdminTagCategoryController`
- **Route:** `admin/tag-categories`
- **Resource name:** `admin.tag-categories`
- **Route parameter:** `tag_category`

### 3.4 Requests, views, and lang
- **Request:** `TagCategoryRequest`
- **Views:** `admin/taxonomy/tag-categories` (index, show, create, edit)
- **Lang keys:** `view.admin.taxonomy.categories` → `view.admin.taxonomy.tag_categories`; `app.taxonomy.category` → `app.taxonomy.tag_category`
- **Variables in views:** `$tagCategories`, `$tagCategory`

### 3.5 Other
- **Tag model:** fillable and relation with `tag_category_id` and `TagCategory::class`
- **TagCategorySeeder**
- **TagFactory:** `tag_category_id`
- **Removed:** old `Category` model (Taxonomy)

---

## 4. Item categories (catalog)

### 4.1 Database
- **Table:** `sections` → `item_categories`
- **Table `items`:** column `section_id` → `category_id` (FK to `item_categories.id`)

### 4.2 Model
- **Class:** `App\Models\Catalog\Section` → `App\Models\Catalog\ItemCategory`
- **File:** `app/Models/Catalog/ItemCategory.php`
- **Table:** `item_categories`
- **Relation in Item:** `section()` → `category()`, FK `category_id`

### 4.3 Controller and routes
- **Controller:** `AdminItemCategoryController`
- **Route:** `admin/item-categories`
- **Resource name:** `admin.item-categories`
- **Route parameter:** `item_category`

### 4.4 Requests, views, and lang
- **Request:** `ItemCategoryRequest`
- **Views:** `admin/catalog/item-categories` (index, show, create, edit)
- **Lang keys:** `view.admin.catalog.sections` → `view.admin.catalog.item_categories`; flash messages `app.catalog.section` → `app.catalog.item_category`
- **Variables in views:** `$itemCategories`, `$itemCategory`
- **JS:** `delete_section` → `delete_item_category`, `.deleteSectionButton` → `.deleteItemCategoryButton`

### 4.5 Other
- **Item model:** fillable and relation with `category_id` and `ItemCategory::class`
- **ItemCategorySeeder**, **ItemCategoryFactory**
- **factory_model_map:** `ItemCategory::class` → `ItemCategoryFactory::class`
- **Removed:** old `Section` model (Catalog)

---

## 5. Collaborators (ex-proprietaries)

### 5.1 Database
- **Table:** `proprietaries` → `collaborators`
- **Column `is_admin`:** removed; replaced by **`role`** (string: `internal` | `external`, default `external`) in `2026_03_01_000002_create_collaborators_table`
- **Table `items`:** column `proprietary_id` → `collaborator_id` (FK to `collaborators.id`)
- **Table `extras`:** column `proprietary_id` → `collaborator_id` (FK to `collaborators.id`)

### 5.2 CollaboratorRole enum
- **Enum:** `App\Enums\CollaboratorRole` (backed string: `internal`, `external`)
- **INTERNAL:** institutional collaborators (e.g. UTFPR, Unicentro); their email cannot be used by external contributors
- **EXTERNAL:** external people who only provide email and send contributions
- **Business rule:** no hardcoded emails; external users cannot register or submit an email already used by an INTERNAL collaborator

### 5.3 Model
- **Class:** `App\Models\Collaborator\Collaborator`
- **File:** `app/Models/Collaborator/Collaborator.php`
- **Table:** `collaborators`
- **Fillable:** `full_name`, `contact`, `role`, `blocked`
- **Casts:** `role` → `CollaboratorRole` enum

### 5.4 Controller and routes
- **Controller:** `AdminCollaboratorController`
- **Store request:** `StoreCollaboratorRequest` (validates `role` required, `internal`|`external`)
- **Route:** `admin/collaborators`
- **Resource name:** `admin.collaborators`
- **Route parameter:** `collaborator`
- **Index search:** role filter by exact value `internal` or `external`

### 5.5 Requests, views, and lang
- **Requests:** `CollaboratorRequest` (public form: contact must not be INTERNAL), `StoreCollaboratorRequest` (admin create), `UpdateCollaboratorRequest` (admin edit, includes `role`)
- **Views:** `admin/collaborators` (index, show, create, edit) with **role** column/field and labels
- **Lang:** `view.admin.collaborator.collaborators.*.role`, `app.collaborator.role.internal` / `app.collaborator.role.external`, `app.collaborator.contact_reserved_for_internal`
- **Validation:** `proprietary_id` → `collaborator_id`
- **JS:** `delete_proprietary` → `delete_collaborator`, `.deleteProprietaryButton` → `.deleteCollaboratorButton`
- **Catalog views:** `search_option_proprietary` → `search_option_collaborator`
- **Variables:** `$collaborators`, `$collaborator`

### 5.6 Item and Extra models
- **Item:** `collaborator_id`, relation `collaborator()`
- **Extra:** `collaborator_id`, relation `collaborator()`

### 5.7 Validation and behaviour
- **Public contribution:** `CollaboratorRequest` forbids contact (email) if it already exists as an INTERNAL collaborator
- **ItemContributionService:** new collaborators from public form get `role = EXTERNAL`; if existing collaborator is INTERNAL, contribution is rejected with `contact_reserved_for_internal`
- **QueryController::checkContact:** returns collaborator only when `role = EXTERNAL` and not blocked (internal emails are not “found” by public)
- **RegisterController:** creates collaborator with `role = INTERNAL` (institutional registration)

### 5.8 Middleware
- **Middleware:** `ValidateProprietary` → `ValidateCollaborator`
- **Alias:** `validate.collaborator`

### 5.9 Other
- **ItemContributionService:** uses `app.collaborator` lang and `CollaboratorRole`
- **config/lockable_routes.php:** `admin.collaborators` with model `Collaborator`
- **CollaboratorFactory:** default `role` = `EXTERNAL`
- **CollaboratorSeeder:** UNICENTRO and UTFPR created with `role = INTERNAL`; no hardcoded email logic
- **Removed:** old proprietary models, controllers, requests, views, lang files

---

## 6. Item–component relation (catalog)

### 6.1 Consistency
- **Route:** `admin/components` → `admin/item-components`
- **Controller:** `AdminComponentController` → `AdminItemComponentController`
- **Views:** `admin/catalog/catalog-components` → `admin/catalog/item-components` (index, show, create)
- **Route names:** `admin.components.*` → `admin.item-components.*`
- **Variables in controller/views:** `$component`/`$components` → `$itemComponent`/`$itemComponents` where appropriate
- **Model:** remains `ItemComponent` (table `item_component`)
- **Removed:** `AdminComponentController`, `admin/catalog/catalog-components` views

---

## 7. Item–tag relation (catalog)

### 7.1 Naming consistency
- **Model:** `TagItem` → `ItemTag` (`App\Models\Catalog\ItemTag`)
- **Table:** `tag_item` → `item_tag`
- **Controller:** `AdminItemTagController` (unchanged name; uses `ItemTag` model)
- **Route:** `admin/item-tags` (unchanged)
- **Views:** `admin/catalog/item-tags` (unchanged)
- **Relations:** In `Item`, `tagItems()` → `itemTags()` (hasMany `ItemTag`); in `Tag`, `items()` still uses pivot table `item_tag`
- **Factory:** `TagItemFactory` (Taxonomy) removed; `ItemTagFactory` in `Database\Factories\Catalog`
- **factory_model_map:** `ItemTag::class` → `ItemTagFactory::class`
- **TagSeeder:** uses `ItemTag::factory()`
- **ItemIndexQueryBuilder, views:** references to `tag_item` → `item_tag`; view columns `tag_item_*` → `item_tag_*`
- **Removed:** `TagItem` model, `TagItemFactory` (Taxonomy)

---

## 8. Migrations

### 8.1 Consolidated schema
All tables are created in one pass with final names; no rename or “alter” migrations.

- **Migrations dated 2026_03_01:**  
  `000001_create_admins_table`, `000002_create_collaborators_table`, `000003_create_item_categories_table`, `000004_create_tag_categories_table`, `000005_create_items_table`, `000006_create_tags_table` (includes `item_tag`), `000007_create_extras_table`, `000008_create_locks_table`, `000009_create_item_component_table`, `000010_create_sessions_table`.

- **Older migrations kept:**  
  `create_password_resets_table`, `create_failed_jobs_table`.

### 8.2 Final schema (reference)
- **admins:** id, username, password, timestamps
- **tag_categories:** id, name, timestamps
- **tags:** id, name, validation, tag_category_id (FK), timestamps
- **item_tag:** id, tag_id, item_id, validation, timestamps
- **item_categories:** id, name, timestamps
- **items:** id, name, description, history, detail, date, identification_code, image (nullable), validation, category_id (FK item_categories), collaborator_id (FK collaborators), timestamps
- **collaborators:** id, full_name, contact, blocked, timestamps
- **extras:** id, info, item_id, collaborator_id, validation, timestamps
- **item_component:** id, item_id, component_id, validation, timestamps
- **locks:** id, lockable (morphs), admin_id (FK admins), expiry_date, timestamps
- **sessions:** id, admin_id (FK admins), ip_address, user_agent, payload, last_activity

### 8.3 Removed migrations
- Rename migrations (categories→tag_categories, sections→item_categories, users→admins, proprietaries→collaborators, tag_item→item_tag)
- `make_items_image_nullable` (nullable image is in the create migration)

---

## 9. Language and translations

### 9.1 Admin (identity)
- **Keys:** `view.admin.identity.users` → `view.admin.identity.admins`; `app.identity.user` → `app.identity.admin`
- **Files:** `lang/.../view/admin/identity.php` (key `admins`), `lang/.../app/identity.php` (key `admin`)

### 9.2 Collaborators
- **Keys:** `view.admin.proprietary.proprietaries` → `view.admin.collaborator.collaborators`; `app.proprietary` → `app.collaborator`
- **Files:** `view/admin/proprietary.php` and `app/proprietary.php` replaced by `view/admin/collaborator.php` and `app/collaborator.php` (pt_BR and en)

### 9.3 Catalog (item categories and items)
- **Keys:** `view.admin.catalog.sections` → `view.admin.catalog.item_categories`; `add_section` → `add_item_category`
- **Keys in items/extras/components:** `section` → `item_category`, `search_option_section` → `search_option_item_category`, `sort_section` → `sort_item_category`, `search_option_proprietary` → `search_option_collaborator`
- **Flash messages:** `app.catalog.section` removed; `app.catalog.item_category` used for item category CRUD

### 9.4 Taxonomy
- **Keys:** `view.admin.taxonomy.categories` → `view.admin.taxonomy.tag_categories`; `add_category` → `add_tag_category`
- **Flash messages:** `app.taxonomy.category` → `app.taxonomy.tag_category`

### 9.5 Validation and JS
- **Validation attributes:** `proprietary_id` → `collaborator_id`; `section_id` → `category_id` (item category); `tag_category_id` added
- **JS warnings (lang/js):** `delete_proprietary` → `delete_collaborator`, `delete_section` → `delete_item_category`, `delete_admin` added

---

## 10. Config and global files

### 10.1 Routes (`routes/web.php`)
- Resource `admin/admins` → `AdminController` (only index, create, store, show, destroy)
- Delete-lock route: `admin/admins/{id}/delete-lock` → `admin.admins.delete-lock`
- Resources: `admin/item-categories`, `admin/item-components`, `admin/tag-categories`, `admin/tags`, `admin/item-tags`, `admin/collaborators`, `admin/extras`, `admin/items`
- Middleware for contribution routes: `validate.collaborator`

### 10.2 Lockable routes (`config/lockable_routes.php`)
- `admin.items`, `admin.tags`, `admin.tag-categories`, `admin.collaborators`, `admin.extras`, `admin.item-categories` with corresponding route parameter and model

### 10.3 Factory model map (`config/factory_model_map.php`)
- Mappings for Item, ItemCategory, ItemComponent, Extra, ItemTag, Tag, Collaborator (and Admin if used)

### 10.4 Seeders (`database/seeders/DatabaseSeeder.php`)
- Calls: `CollaboratorSeeder`, `AdminSeeder`, `TagCategorySeeder`, `ItemCategorySeeder`, `ItemSeeder`, `ExtraSeeder`, `TagSeeder`

---

## 11. Post-refactoring checklist

- [ ] Run `php artisan migrate` (or `migrate:fresh` for clean install)
- [ ] Run seeders if needed: `php artisan db:seed`
- [ ] Set `SESSION_DRIVER=database` in `.env` to use session table with `admin_id`
- [ ] Clear caches: `php artisan config:clear`, `php artisan cache:clear`
- [ ] Verify login with `Admin` model and auth config (`admins` guard/provider)
- [ ] Test CRUD for tag categories, item categories, item components, item tags, collaborators, admins, items, extras
- [ ] Test locks and release-lock for updated types
- [ ] If sessions table already had `user_id`, run a migration to rename to `admin_id` or use `migrate:fresh`

---

*Document generated from the refactoring of tables, models, controllers, routes, views, lang, and config in the E-Museu project.*
