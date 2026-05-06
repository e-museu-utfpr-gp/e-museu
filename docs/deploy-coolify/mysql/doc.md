# MySQL on Coolify

Create a **MySQL** (or MariaDB, if you standardize on it) service in Coolify for staging and/or production. Use credentials and a database name consistent with `DATABASE_URL` in:

- `docs/deploy-coolify/coolify-staging.env.example` — example DB name `e_museu_staging`
- `docs/deploy-coolify/coolify-production.env.example` — example DB name `e_museu_production`

## After the database service exists

Coolify provisions an **empty** database. You must **import your schema and data** (for example a `.sql` dump from `mysqldump` or a migration baseline from another environment).

Until the dump (or migrations + seeders) has been applied, the app will not have the expected tables or content.
