<?php

declare(strict_types=1);

namespace App\Actions\Catalog;

use App\Http\Requests\Admin\Catalog\AdminUpdateItemRequest;
use App\Models\Catalog\Item;
use App\Services\Catalog\{ItemImagesService, ItemQrCodeService, ItemService};
use Illuminate\Support\Arr;
use Throwable;

/**
 * Applies admin item update payload, image side-effects, and optional QR regeneration when the code changes.
 */
final class UpdateAdminItemFromRequestAction
{
    public function __construct(
        private readonly ItemService $itemService,
        private readonly ItemImagesService $itemImagesService,
        private readonly ItemQrCodeService $itemQrCodeService,
    ) {
    }

    public function handle(Item $item, AdminUpdateItemRequest $request): void
    {
        $originalIdentificationCode = (string) $item->identification_code;

        $data = Arr::except($request->validated(), [
            'image',
            'gallery_images',
            'delete_image_ids',
            'set_cover_image_id',
        ]);
        if (! array_key_exists('date', $data)) {
            $data['date'] = $request->input('date');
        }

        $this->itemImagesService->processDeleteImageIds($item, $request);
        $this->itemImagesService->processCoverImage($item, $request);
        $this->itemImagesService->processGalleryImages($item, $request);

        $this->itemService->updateItem($item, $data);
        $item->refresh();

        if ((string) $item->identification_code !== $originalIdentificationCode) {
            try {
                $this->itemQrCodeService->regenerateForItem($item);
            } catch (Throwable $e) {
                report($e);
            }
        }
    }
}
