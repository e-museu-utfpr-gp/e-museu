<?php

namespace Database\Seeders\Catalog;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use App\Models\Catalog\Item;
use App\Models\Catalog\ItemComponent;

class ItemSeeder extends Seeder
{
    /** Fallback image path (relative to public/) when no existing item image is found. */
    private const DEFAULT_IMAGE_PATH = 'img/banner.png';

    public function run(): void
    {
        if (! app()->environment('local')) {
            return;
        }

        $contents = $this->getSeedImageContents();
        $items = Item::factory(100)->create();

        if ($contents !== null) {
            foreach ($items as $item) {
                $path = Item::buildImagePath($item);
                Storage::disk('public')->put($path, $contents);
                $item->update(['image' => $path]);
            }
        }

        ItemComponent::factory(50)->create();
    }

    /**
     * Image contents for seeded items: from an existing item in storage, or from default path.
     */
    private function getSeedImageContents(): ?string
    {
        $existing = Item::whereNotNull('image')
            ->where('image', '!=', '')
            ->where('image', 'not like', 'http%')
            ->first();

        if ($existing !== null && Storage::disk('public')->exists($existing->getRawOriginal('image'))) {
            return Storage::disk('public')->get($existing->getRawOriginal('image'));
        }

        $path = public_path(self::DEFAULT_IMAGE_PATH);
        if (is_file($path)) {
            return file_get_contents($path);
        }

        return null;
    }
}
