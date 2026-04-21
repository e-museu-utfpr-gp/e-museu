<?php

declare(strict_types=1);

namespace App\Actions\Catalog\StoreItemContribution\Concerns;

use Illuminate\Http\UploadedFile;

trait PreparesContributionItemPayload
{
    /**
     * @param  array<string, mixed>  $itemData
     * @return array<string, mixed>
     */
    private function itemDataWithoutCoverUploadFields(array $itemData, ?UploadedFile $coverImage): array
    {
        if ($coverImage !== null) {
            unset($itemData['image'], $itemData['cover_image']);
        }

        return $itemData;
    }
}
