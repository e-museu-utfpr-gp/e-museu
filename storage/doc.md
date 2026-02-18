# Storage

This directory holds Laravel **framework** data: cache, logs, sessions, queues, compiled views, etc. **Public files** (images, uploads) are stored here only in **local** and **prod-local (Docker)** — under `storage/app/public`. In staging and production the public disk is S3. Public files are never served directly from the filesystem; they always go through the app, so, we don't need to use `php artisan storage:link`. (see below).

## Images and files (public disk)

User-uploaded images and other public files are stored on the **`public` disk** (see `config/filesystems.php`). They are **always served through the application** via the **StorageProxyController** (`app/Http/Controllers/StorageProxyController.php`).

- URLs look like: `{APP_URL}/storage/{path}` (e.g. `/storage/items/123.png`).
- The proxy checks the file exists on the `public` disk, then streams it to the client. The bucket or local path is never exposed in the response.
- **We do not use** `php artisan storage:link`. The `public/storage` symlink is not created. All requests to `/storage/...` hit the Laravel route and go through the proxy, so the same behaviour applies in every environment.

## Environments

| Environment   | Public disk backend | Notes |
|---------------|---------------------|--------|
| **Staging / Production** | S3 (or MinIO) | `FILESYSTEM_DISK=s3`. Bucket can be internal-only; the app fetches and streams via the proxy. |
| **Local**     | Local disk          | `FILESYSTEM_DISK=local` (works without). Files live under `storage/app/public`. |
| **Prod-local (Docker)** | Local disk on volume | Same as local, but `storage` (or the relevant subpath) is mounted as a Docker volume so data persists and is shared between the app container and host as defined in `docker-compose`. |
