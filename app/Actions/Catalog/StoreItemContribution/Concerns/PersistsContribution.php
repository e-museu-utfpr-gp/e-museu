<?php

declare(strict_types=1);

namespace App\Actions\Catalog\StoreItemContribution\Concerns;

use App\Models\Catalog\Item;
use App\Models\Collaborator\Collaborator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Throwable;

trait PersistsContribution
{
    /**
     * Persists a validated public item contribution (HTTP or programmatic callers with the same payload shape).
     *
     * Early-return statuses must stay in sync with
     * {@see \App\Support\Catalog\PublicCatalogContributionOutcome::throwUnlessOk()} so new values surface as
     * validation errors, not generic server errors.
     *
     * On success, {@see \App\Services\Catalog\ItemQrCodeService::regenerateForItem()} runs after the DB transaction
     * commits (synchronous, same request) so the transaction is not held during external HTTP.
     *
     * @param  array<string, mixed>  $collaboratorData
     * @param  array<string, mixed>  $itemPayload
     * @param  array<int, array<string, mixed>>  $tags
     * @param  array<int, array<string, mixed>>  $extras
     * @param  array<int, array<string, mixed>>  $components
     * @param  array<int, UploadedFile>|null  $galleryFiles
     * @return (
     *     array{status: 'ok', item: Item}
     *     |array{status: 'internal_blocked'|'collaborator_blocked'|'email_unverified'}
     * )
     */
    public function persistContribution(
        array $collaboratorData,
        array $itemPayload,
        int $contentLanguageId,
        array $tags,
        array $extras,
        array $components,
        ?UploadedFile $coverImage,
        ?array $galleryFiles,
    ): array {
        $collaboratorOrEarly = $this->collaboratorForContributionOrEarlyStatus($collaboratorData);
        if (! $collaboratorOrEarly instanceof Collaborator) {
            return $collaboratorOrEarly;
        }
        $collaborator = $collaboratorOrEarly;

        $itemData = $this->itemDataWithoutCoverUploadFields($itemPayload, $coverImage);
        $galleryImages = $galleryFiles ?? [];
        $catalogRelations = [
            'tags' => $tags,
            'extras' => $extras,
            'components' => $components,
        ];
        $cleanup = ['itemId' => null];

        try {
            $result = DB::transaction(function () use (
                &$cleanup,
                $collaborator,
                $collaboratorData,
                $itemData,
                $contentLanguageId,
                $catalogRelations,
                $coverImage,
                $galleryImages,
            ): array {
                return $this->completeContributionWithinTransaction(
                    $collaborator,
                    $collaboratorData,
                    $itemData,
                    $contentLanguageId,
                    $catalogRelations,
                    $coverImage,
                    $galleryImages,
                    $cleanup,
                );
            });

            try {
                $this->itemQrCodeService->regenerateForItem($result['item']);
            } catch (Throwable $e) {
                report($e);
            }

            return $result;
        } catch (Throwable $e) {
            $itemId = $cleanup['itemId'] ?? null;
            if (is_int($itemId) && $itemId > 0) {
                $this->itemImagesService->deletePublicStorageFolderForItemId($itemId);
            }

            throw $e;
        }
    }
}
