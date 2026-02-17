<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\UnableToCheckExistence;
use League\Flysystem\UnableToReadFile;

/**
 * Serves files from the public disk via app URL (domain + /storage/...).
 * When using S3/MinIO, the app fetches internally and streams to the client,
 * so MinIO can stay on internal access only.
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
            if (! Storage::disk('public')->exists($path)) {
                abort(404);
            }

            return Storage::disk('public')->response($path);
        } catch (UnableToCheckExistence | UnableToReadFile $e) {
            Log::warning('Storage proxy: could not serve file.', [
                'path' => $path,
                'message' => $e->getMessage(),
                'previous' => $e->getPrevious()?->getMessage(),
            ]);

            abort(404);
        }
    }
}
