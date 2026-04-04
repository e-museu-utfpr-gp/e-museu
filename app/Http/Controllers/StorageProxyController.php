<?php

namespace App\Http\Controllers;

use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Log, Storage};
use League\Flysystem\{UnableToCheckExistence, UnableToReadFile};

/**
 * Serves files from the public disk via app URL (domain + /storage/...).
 * When using S3/MinIO, the app fetches internally and streams to the client,
 * so MinIO can stay on internal access only.
 *
 * For local disks, resolved paths must stay under the configured root (mitigates odd paths / symlinks).
 */
class StorageProxyController extends Controller
{
    public function __invoke(Request $request, string $path): mixed
    {
        $path = str_replace('\\', '/', $path);

        if ($path === '' || str_contains($path, '..')) {
            abort(404);
        }

        try {
            $disk = Storage::disk('public');

            if (! $disk->exists($path)) {
                abort(404);
            }

            $this->abortIfLocalResolvedPathEscapesRoot($disk, $path);

            return $disk->response($path);
        } catch (UnableToCheckExistence | UnableToReadFile $e) {
            Log::warning('Storage proxy: could not serve file.', [
                'path' => $path,
                'message' => $e->getMessage(),
                'previous' => $e->getPrevious()?->getMessage(),
            ]);

            abort(404);
        }
    }

    private function abortIfLocalResolvedPathEscapesRoot(FilesystemAdapter $disk, string $path): void
    {
        if (config('filesystems.disks.public.driver') !== 'local') {
            return;
        }

        $root = config('filesystems.disks.public.root');
        if (! is_string($root) || $root === '') {
            abort(404);
        }

        $rootReal = realpath($root);
        $absolute = realpath($disk->path($path));
        if ($rootReal === false || $absolute === false) {
            abort(404);
        }

        $rootReal = rtrim($rootReal, DIRECTORY_SEPARATOR);
        $underRoot = $absolute === $rootReal
            || str_starts_with($absolute, $rootReal . DIRECTORY_SEPARATOR);
        if (! $underRoot) {
            abort(404);
        }
    }
}
