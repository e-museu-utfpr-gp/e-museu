# Storage

> **Last updated:** 2026-04-21  
> Related: `config/filesystems.php`, `app/Http/Controllers/StorageProxyController.php`, route `GET /storage/{path}` in `routes/web.php`. Deploy examples: `docs/deploy/`.

## What lives under `storage/`

This directory holds Laravel **framework** data: cache, logs, sessions, queues, compiled views, etc.

**Public uploads** (images, user files) use the **`public` filesystem disk**. On non-S3 setups they are stored under `storage/app/public`. In staging/production the same logical disk may be backed by **S3 or MinIO** (see `FILESYSTEM_DISK` in `config/filesystems.php`).

## Serving public files (always via the app)

Public files are **never** exposed as raw filesystem or direct bucket URLs to browsers. They are served through **`StorageProxyController`**, so you **do not** run `php artisan storage:link`. There is no reliance on a `public/storage` symlink.

- **URL shape:** `{APP_URL}/storage/{path}` (e.g. `/storage/items/123/cover.png`).
- **Behaviour:** the controller resolves the path on the `public` disk, checks existence, then streams the response. For S3/MinIO the app reads from the bucket internally and streams to the client, so the bucket can remain private.
- **Safety:** requests containing `..` are rejected with 404. For **local** disks, resolved paths are verified to stay under the configured disk root (path escape / odd symlinks).

## Configuration summary

| `FILESYSTEM_DISK` (env) | `public` disk backend |
|-------------------------|------------------------|
| Not `s3` (typical local / prod-local Docker) | Local path `storage/app/public` |
| `s3` | S3-compatible API (AWS S3 or MinIO), credentials and bucket from `AWS_*` env vars |

The `public` disk `url` is set to `APP_URL` + `/storage` so generated URLs match the proxy route.

## Environments (operational)

| Environment | Typical backend | Notes |
|-------------|-----------------|--------|
| **Local** | Local disk | Files under `storage/app/public`. |
| **Prod-local (Docker)** | Local disk on volume | `storage` (or subpaths) mounted so data persists between container and host per `docker-compose`. |
| **Staging / production** | S3 or MinIO | Set `FILESYSTEM_DISK=s3` and `AWS_*` / endpoint as needed. See `docs/deploy/` for Coolify-oriented examples. |

## For AI / contributors

- New code that stores **public** assets should use `Storage::disk('public')` (or helpers that target that disk), not ad-hoc paths.
- User-facing strings stay in translation files; this doc is technical English only.

## Change log (short)

- **2026-04-21**: Expanded with security notes (`..` rejection, local root check), `FILESYSTEM_DISK` table, route/controller references, deploy pointer, and alignment with English-only technical docs.
