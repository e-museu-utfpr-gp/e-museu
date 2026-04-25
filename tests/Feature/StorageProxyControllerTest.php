<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StorageProxyControllerTest extends TestCase
{
    public function test_storage_proxy_returns_not_found_when_path_contains_parent_segments(): void
    {
        $this->get('/storage/foo/../bar')->assertNotFound();
    }

    public function test_storage_proxy_returns_not_found_when_path_contains_double_dot_substring(): void
    {
        $this->get('/storage/file..name.txt')->assertNotFound();
    }

    public function test_storage_proxy_returns_not_found_for_missing_file(): void
    {
        Storage::fake('public');

        $this->get('/storage/does-not-exist.txt')->assertNotFound();
    }

    public function test_storage_proxy_streams_existing_public_file(): void
    {
        $previousRoot = (string) config('filesystems.disks.public.root');
        $root = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'e_museu_storage_proxy_' . uniqid('', true);
        File::ensureDirectoryExists($root);

        config(['filesystems.disks.public.root' => $root]);
        Storage::forgetDisk('public');

        $relative = 'proxied/hello_' . uniqid('', false) . '.txt';
        Storage::disk('public')->put($relative, 'hello-storage-proxy');

        try {
            $response = $this->get('/storage/' . $relative);
            $response->assertOk();
            $this->assertSame('hello-storage-proxy', $response->streamedContent());
        } finally {
            Storage::forgetDisk('public');
            config(['filesystems.disks.public.root' => $previousRoot]);
            Storage::forgetDisk('public');
            if (File::isDirectory($root)) {
                File::deleteDirectory($root);
            }
        }
    }
}
