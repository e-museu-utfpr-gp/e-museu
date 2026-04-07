<?php

namespace Database\Seeders\Catalog;

use App\Enums\Catalog\ItemImageType;
use App\Models\Catalog\{Item, ItemComponent, ItemImage};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class ItemSeeder extends Seeder
{
    /** Fallback image path (relative to public/) when no existing item image is found. */
    private const DEFAULT_IMAGE_PATH = 'img/banner.png';

    /** Number of items to create. */
    private const ITEMS_COUNT = 100;

    /** Min/max extra gallery images per item (when seed image is available). */
    private const GALLERY_MIN = 0;

    private const GALLERY_MAX = 4;

    /** Fraction of items that get extra gallery images (0–1). */
    private const ITEMS_WITH_GALLERY_RATIO = 0.4;

    public function run(): void
    {
        if (! app()->environment('local')) {
            return;
        }

        $imageContents = $this->getSeedImageContents();
        $items = Item::factory(self::ITEMS_COUNT)->create();

        if ($imageContents !== null) {
            $itemsWithGallery = (int) ceil(self::ITEMS_COUNT * self::ITEMS_WITH_GALLERY_RATIO);

            foreach ($items as $index => $item) {
                $this->seedItemImages($item, $imageContents, $index < $itemsWithGallery);
            }
        }

        ItemComponent::factory(50)->create();
    }

    /**
     * Create cover image and optionally multiple gallery images for an item.
     */
    private function seedItemImages(Item $item, string $imageContents, bool $withGallery): void
    {
        $path = ItemImage::buildPath($item, 'png');
        Storage::disk('public')->put($path, $imageContents);
        $item->images()->create([
            'path' => $path,
            'type' => ItemImageType::COVER,
            'sort_order' => 0,
        ]);

        if (! $withGallery) {
            return;
        }

        $galleryCount = random_int(self::GALLERY_MIN, self::GALLERY_MAX);
        if ($galleryCount < 1) {
            return;
        }

        for ($i = 1; $i <= $galleryCount; $i++) {
            $galleryPath = ItemImage::buildPath($item, 'png');
            Storage::disk('public')->put($galleryPath, $imageContents);
            $item->images()->create([
                'path' => $galleryPath,
                'type' => ItemImageType::GALLERY,
                'sort_order' => $i,
            ]);
        }
    }

    /**
     * Image contents for seeded items: from an existing item image in storage, or from default path.
     */
    private function getSeedImageContents(): ?string
    {
        $existing = ItemImage::whereNotNull('path')
            ->where('path', '!=', '')
            ->where('path', 'not like', 'http%')
            ->first();

        if ($existing !== null && Storage::disk('public')->exists($existing->getRawOriginal('path'))) {
            return Storage::disk('public')->get($existing->getRawOriginal('path'));
        }

        $path = public_path(self::DEFAULT_IMAGE_PATH);
        if (is_file($path)) {
            return file_get_contents($path);
        }

        return null;
    }
}
