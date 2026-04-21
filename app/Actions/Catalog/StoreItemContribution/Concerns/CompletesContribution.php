<?php

declare(strict_types=1);

namespace App\Actions\Catalog\StoreItemContribution\Concerns;

use App\Models\Catalog\Item;
use App\Models\Collaborator\Collaborator;
use Illuminate\Http\UploadedFile;

/**
 * Item row, images, and catalog relations for one contribution.
 * Runs inside the transaction opened by {@see PersistsContribution::persistContribution()}.
 * QR generation runs after that transaction commits (same action request), not here.
 */
trait CompletesContribution
{
    /**
     * @param  array<string, mixed>  $collaboratorData
     * @param  array<string, mixed>  $itemData
     * @param  array{
     *     tags: array<int, array<string, mixed>>,
     *     extras: array<int, array<string, mixed>>,
     *     components: array<int, array<string, mixed>>,
     * }  $catalogRelations
     * @param  array<int, UploadedFile>  $galleryImages
     * @param  array{itemId: int|null}  $cleanup
     * @return array{status: 'ok', item: Item}
     */
    private function completeContributionWithinTransaction(
        Collaborator $collaborator,
        array $collaboratorData,
        array $itemData,
        int $contentLanguageId,
        array $catalogRelations,
        ?UploadedFile $coverImage,
        array $galleryImages,
        array &$cleanup,
    ): array {
        $this->contributionCollaborator->applySubmittedFullNameAfterVerifiedContribution(
            $collaborator,
            $collaboratorData,
        );
        $collaborator->refresh();

        $item = $this->createItemWithImages(
            $itemData,
            $collaborator,
            $contentLanguageId,
            $coverImage,
            $galleryImages,
            $cleanup,
        );

        $this->attachContributedCatalogRelations($item, $collaborator, $catalogRelations, $contentLanguageId);

        return [
            'status' => 'ok',
            'item' => $item,
        ];
    }

    /**
     * @param  array{
     *     tags: array<int, array<string, mixed>>,
     *     extras: array<int, array<string, mixed>>,
     *     components: array<int, array<string, mixed>>,
     * }  $catalogRelations
     */
    private function attachContributedCatalogRelations(
        Item $item,
        Collaborator $collaborator,
        array $catalogRelations,
        int $contentLanguageId,
    ): void {
        $this->itemTagService->attachTagsToItem($item, $catalogRelations['tags'], $contentLanguageId);
        $this->extraService->createForItem($item, $collaborator, $catalogRelations['extras'], $contentLanguageId);
        $this->itemComponentService->attachContributedComponents($item, $catalogRelations['components']);
    }
}
