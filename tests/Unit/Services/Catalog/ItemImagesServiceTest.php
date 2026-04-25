<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Catalog;

use App\Services\Catalog\ItemImagesService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Group;
use Tests\Support\MinimalContributionCoverJpeg;
use Tests\TestCase;

#[Group('services')]
class ItemImagesServiceTest extends TestCase
{
    public function test_filter_valid_gallery_files_returns_only_valid_uploads(): void
    {
        $jpeg = MinimalContributionCoverJpeg::binary();
        $valid = UploadedFile::fake()->createWithContent('a.jpg', $jpeg);

        $svc = app(ItemImagesService::class);

        // Intentionally mixes invalid entries; runtime accepts mixed input from HTTP.
        // @phpstan-ignore argument.type
        $out = $svc->filterValidGalleryFiles([$valid, 'not-a-file']);

        $this->assertCount(1, $out);
        $this->assertSame($valid, $out[0]);
    }

    public function test_filter_valid_gallery_files_returns_empty_for_null(): void
    {
        $svc = app(ItemImagesService::class);
        $this->assertSame([], $svc->filterValidGalleryFiles(null));
    }

    public function test_delete_public_storage_folder_for_item_id_no_ops_for_non_positive_id(): void
    {
        Storage::fake('public');

        $svc = app(ItemImagesService::class);
        $svc->deletePublicStorageFolderForItemId(0);
        $svc->deletePublicStorageFolderForItemId(-1);

        Storage::disk('public')->assertMissing('items/0');
    }
}
