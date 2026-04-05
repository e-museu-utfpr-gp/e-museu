<?php

namespace App\Actions\Catalog;

use App\Models\Catalog\Item;
use App\Models\Collaborator\Collaborator;
use App\Services\Catalog\{ExtraService, ItemComponentService, ItemImagesService, ItemService, ItemTagService};
use App\Services\Collaborator\CollaboratorService;
use App\Services\Taxonomy\TagService;
use App\Support\Content\TranslatablePayload;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Store a public catalog contribution (item, images, tags, extras, components).
 */
final class StoreItemContributionAction
{
    public function __construct(
        private readonly CollaboratorService $collaboratorService,
        private readonly ExtraService $extraService,
        private readonly ItemComponentService $itemComponentService,
        private readonly ItemTagService $itemTagService,
        private readonly TagService $tagService,
        private readonly ItemService $itemService,
        private readonly ItemImagesService $itemImagesService,
    ) {
    }

    /**
     * Store a contributed item (public contribution flow).
     * Resolves collaborator and creates item, images, tags, extras and components in one database transaction.
     *
     * @param  array<string, mixed>  $collaboratorData  Collaborator data validated by the
     *                                                  request (e.g. name, email).
     * @param  array<string, mixed>  $itemData          Item data (name, category_id,
     *                                                  description, history, detail, date,
     *                                                  validation, etc.).
     * @param  array<int, array<string, mixed>>  $tags  Tag data validated by the request.
     * @param  array<int, array<string, mixed>>  $extras  Extra data (e.g. type, value, etc.).
     * @param  array<int, array<string, mixed>>  $components  Components identified by
     *                                                        category and name.
     * @param  array<int, UploadedFile>|null  $galleryImages
     * @return array{
     *     status: 'ok'|'internal_blocked'|'collaborator_blocked'|'email_unverified',
     *     item?: Item,
     * }
     */
    public function handle(
        array $collaboratorData,
        array $itemData,
        int $contentLanguageId,
        array $tags,
        array $extras,
        array $components,
        ?UploadedFile $coverImage,
        ?array $galleryImages = null
    ): array {
        $resolution = $this->resolveCollaboratorForContribution($collaboratorData);
        if ($resolution['status'] !== 'ok') {
            return ['status' => $resolution['status']];
        }
        $collaborator = $resolution['collaborator'];
        if (! $collaborator instanceof Collaborator) {
            return ['status' => 'internal_blocked'];
        }

        if ($coverImage) {
            unset($itemData['image'], $itemData['cover_image']);
        }

        /** @var array{itemId: int|null} */
        $cleanup = ['itemId' => null];

        try {
            return DB::transaction(function () use (
                $itemData,
                $collaborator,
                $collaboratorData,
                $contentLanguageId,
                $tags,
                $extras,
                $components,
                $coverImage,
                $galleryImages,
                &$cleanup,
            ): array {
                $this->collaboratorService->applySubmittedFullNameAfterVerifiedContribution(
                    $collaborator,
                    (string) ($collaboratorData['full_name'] ?? ''),
                );
                $collaborator->refresh();

                $item = $this->createItemWithImages(
                    $itemData,
                    $collaborator,
                    $contentLanguageId,
                    $coverImage,
                    $galleryImages ?? [],
                    $cleanup,
                );

                $this->itemTagService->attachTagsToItem($item, $tags, $this->tagService, $contentLanguageId);
                $this->extraService->createForItem($item, $collaborator, $extras, $contentLanguageId);
                $this->itemComponentService->attachContributedComponents($item, $components);

                return [
                    'status' => 'ok',
                    'item' => $item,
                ];
            });
        } catch (Throwable $e) {
            if ($cleanup['itemId'] !== null) {
                $this->itemImagesService->deletePublicStorageFolderForItemId($cleanup['itemId']);
            }

            throw $e;
        }
    }

    /**
     * Resolve collaborator for contribution.
     *
     * @param  array<string, mixed>  $collaboratorData
     * @return array{
     *     status: 'ok'|'internal_blocked'|'collaborator_blocked'|'email_unverified',
     *     collaborator: Collaborator|null,
     * }
     */
    private function resolveCollaboratorForContribution(array $collaboratorData): array
    {
        $collaborator = $this->collaboratorService->findCollaboratorByEmailForPublicLookup(
            (string) ($collaboratorData['email'] ?? ''),
        );
        if ($collaborator === null) {
            return [
                'status' => 'email_unverified',
                'collaborator' => null,
            ];
        }

        if ($collaborator->role === \App\Enums\Collaborator\CollaboratorRole::INTERNAL) {
            return [
                'status' => 'internal_blocked',
                'collaborator' => null,
            ];
        }

        if ($collaborator->blocked === true) {
            return [
                'status' => 'collaborator_blocked',
                'collaborator' => null,
            ];
        }

        $gate = $this->collaboratorService->publicContributionCollaboratorGate($collaborator, $collaboratorData);
        if ($gate === 'email_unverified') {
            return [
                'status' => 'email_unverified',
                'collaborator' => null,
            ];
        }

        return [
            'status' => 'ok',
            'collaborator' => $collaborator,
        ];
    }

    /**
     * Create item in transaction and store cover + gallery images.
     *
     * @param  array<string, mixed>  $itemData
     * @param  array<int, UploadedFile>  $galleryImages
     * @param  array{itemId: int|null}  $cleanup  Item id once the row exists; used to remove
     *                                            public storage if the transaction rolls back.
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
     * Create an item for contribution flow (identification code).
     * Caller must wrap in {@see DB::transaction} together with images, tags, extras, and components.
     *
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
            'identification_code' => $this->itemService->createIdentificationCode($item),
        ]);

        return $item;
    }
}
