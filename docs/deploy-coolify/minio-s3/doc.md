# MinIO (S3-compatible) on Coolify

Compose stacks for **staging** and **production** live next to this file (`docker-compose.staging.yml`, `docker-compose.production.yml`). They run MinIO with the S3 API on port **9000** inside the container and the **web console** on **9001**.

Host port mappings (as in those files):

| Environment | S3 API (maps to 9000) | Console (maps to 9001) |
|-------------|------------------------|-------------------------|
| Staging | `20000` | `20001` |
| Production | `10000` | `10001` |

Coolify can override `MINIO_ROOT_USER` and `MINIO_ROOT_PASSWORD`; align them with `AWS_ACCESS_KEY_ID` and `AWS_SECRET_ACCESS_KEY` in the Laravel resource (`docs/deploy-coolify/coolify-staging.env.example`, `docs/deploy-coolify/coolify-production.env.example`).

## After MinIO is running

1. **Create the bucket** your app will use — the same name as `AWS_BUCKET` in the Laravel env (e.g. `e-museu-staging`, `e-museu-production`).
2. **Load object data into that bucket.** A fresh MinIO volume is empty. If you are migrating from another environment or local `storage`, copy the files that belong on the `public` / S3 disk into the bucket (preserve paths if the app expects specific keys). You can use the MinIO console (**Upload**), the `mc` CLI, or any S3-compatible tool against `AWS_ENDPOINT` (and path-style settings if applicable — see `config/filesystems.php` / `AWS_USE_PATH_STYLE_ENDPOINT` if needed).

Until objects exist in the bucket, features that read files from S3 (catalog images, etc.) will not find them.

## Laravel connection

Point `AWS_ENDPOINT` at the MinIO S3 API URL reachable from the **Laravel** container (often host IP + mapped API port when stacks are separate). See comments in the compose files and the Coolify env examples.
