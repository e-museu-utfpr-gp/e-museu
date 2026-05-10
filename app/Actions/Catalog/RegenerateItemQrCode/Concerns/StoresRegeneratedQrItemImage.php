<?php

declare(strict_types=1);

namespace App\Actions\Catalog\RegenerateItemQrCode\Concerns;

use App\Enums\Catalog\ItemImageType;
use App\Exceptions\ItemQrRegenerateException;
use App\Models\Catalog\Item;
use App\Models\Catalog\ItemImage;
use Illuminate\Support\Facades\Storage;

trait StoresRegeneratedQrItemImage
{
    /**
     * @throws ItemQrRegenerateException
     */
    protected function storeRegeneratedQrItemImage(Item $item, string $targetUrl, string $pngBody): ItemImage
    {
        $path = ItemImage::buildQrCodePath($item, $targetUrl, 'png');
        if (! Storage::disk('public')->put($path, $pngBody)) {
            throw new ItemQrRegenerateException('app.catalog.item.upload_store_failed');
        }

        /** @var ItemImage $img */
        $img = $item->images()->create([
            'path' => $path,
            'type' => ItemImageType::QRCODE,
            'sort_order' => 0,
        ]);

        return $img;
    }
}
