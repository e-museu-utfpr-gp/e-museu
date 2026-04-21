<?php

declare(strict_types=1);

namespace App\Actions\Catalog\StoreItemContribution\Concerns;

use App\Models\Catalog\Item;
use App\Models\Collaborator\Collaborator;
use App\Support\Content\TranslatablePayload;
use Illuminate\Http\UploadedFile;

trait CreatesContributionItem
{
    /**
     * @param  array<string, mixed>  $itemData
     * @param  array<int, UploadedFile>  $galleryImages
     * @param  array{itemId: int|null}  $cleanup
     */
    private function createItemWithImages(
        array $itemData,
        Collaborator $collaborator,
        int $contentLanguageId,
        ?UploadedFile $coverImage,
        array $galleryImages,
        array &$cleanup,
    ): Item {
        $item = $this->createItemForContribution($itemData, $collaborator, $contentLanguageId);
        $cleanup['itemId'] = (int) $item->id;
        if ($coverImage !== null) {
            $this->itemImagesService->storeCoverImage($item, $coverImage);
        }
        $this->itemImagesService->storeGalleryImages($item, $galleryImages);

        return $item;
    }

    /**
     * @param  array<string, mixed>  $itemData
     */
    private function createItemForContribution(
        array $itemData,
        Collaborator $collaborator,
        int $contentLanguageId
    ): Item {
        $itemData['collaborator_id'] = $collaborator->id;
        $itemData['identification_code'] = '000';

        $split = TranslatablePayload::split($itemData, TranslatablePayload::ITEM_KEYS);
        $translationData = $split['translation'];
        $persist = $split['persist'];

        $item = Item::create($persist);
        if ($translationData !== []) {
            $item->syncTranslationForLanguage($contentLanguageId, $translationData);
        }
        $item->update([
            'identification_code' => $this->itemService->createIdentificationCode($item, $contentLanguageId),
        ]);
        $item->refresh();

        return $item;
    }
}
