# Domain models refactor – summary

## What was done

- **Models:** Updated all 10 models to domain namespaces (`App\Models\Catalog`, `Taxonomy`, `Proprietary`, `Identity`) and fixed cross-references.
- **App:** Updated config, controllers, middleware, services, requests, and console command to use the new model classes.
- **Database:** Updated seeders and factories (imports and docblocks).
- **Factory resolution:** In `AppServiceProvider`, registered `guessFactoryNamesUsing` and `guessModelNamesUsing` so Laravel finds the right factory for each model and the right model for each factory.
- **Config & PHPMD:** Added `config/factory_model_map.php` and `config/lockable_routes.php`; `AppServiceProvider` and `CheckLock` read from config to reduce coupling (CBO).

---

## Problems and solutions

| Problem | Solution |
|--------|----------|
| **Seed fails: "Class App\Proprietary not found"** | Laravel inferred the wrong model from the factory name. Registered `guessModelNamesUsing` with a factory→model map (from config) so each factory resolves to the correct model class. |
| **Wrong factory path for models in subnamespaces** | Default resolution expected e.g. `Database\Factories\Catalog\ItemFactory`. Registered `guessFactoryNamesUsing` with a model→factory map so models point to the existing factories in `Database\Factories\`. |
| **PHPMD: CouplingBetweenObjects in CheckLock and AppServiceProvider** | Moved route→model and model→factory mappings to config files so both classes depend on config instead of many model/factory classes directly. |
