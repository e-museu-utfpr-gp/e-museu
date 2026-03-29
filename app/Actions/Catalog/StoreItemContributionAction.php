<?php

namespace App\Actions\Catalog;

use App\Models\Catalog\Item;
use App\Models\Catalog\ItemComponent;
use App\Models\Collaborator\Collaborator;
use App\Services\Catalog\ExtraService;
use App\Services\Catalog\ItemImagesService;
use App\Services\Catalog\ItemService;
use App\Services\Catalog\ItemTagService;
use App\Services\Collaborator\CollaboratorService;
use App\Services\Taxonomy\TagService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class StoreItemContributionAction
{
    public function __construct(
        private readonly CollaboratorService $collaboratorService,
        private readonly ExtraService $extraService,
        private readonly ItemTagService $itemTagService,
        private readonly TagService $tagService,
        private readonly ItemService $itemService,
        private readonly ItemImagesService $itemImagesService,
    ) {
    }

    /**
     * Store a contributed item (public contribution flow).
     * Resolves collaborator and creates item, images, tags, extras and components.
     *
     * @param  array<string, mixed>  $collaboratorData  Collaborator data validated by the
     *                                                  request (e.g. name, contact).
     * @param  array<string, mixed>  $itemData          Item data (name, category_id,
     *                                                  description, history, detail, date,
     *                                                  validation, etc.).
     * @param  array<int, array<string, mixed>>  $tags  Tag data validated by the request.
     * @param  array<int, array<string, mixed>>  $extras  Extra data (e.g. type, value, etc.).
     * @param  array<int, array<string, mixed>>  $components  Components identified by
     *                                                        category and name.
     * @param  array<int, UploadedFile>|null  $galleryImages
     * @return array{
     *     status: 'ok'|'internal_blocked'|'collaborator_blocked',
     *     item?: Item
     * }
     */
    public function handle(
        array $collaboratorData,
        array $itemData,
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

        $item = $this->createItemWithImages(
            $itemData,
            $collaborator,
            $coverImage,
            $galleryImages ?? [],
        );

        $this->itemTagService->attachTagsToItem($item, $tags, $this->tagService);
        $this->extraService->createForItem($item, $collaborator, $extras);
        $this->attachComponentsToItem($item, $components);

        return [
            'status' => 'ok',
            'item' => $item,
        ];
    }

    /**
     * Resolve collaborator for contribution.
     *
     * @param  array<string, mixed>  $collaboratorData
     * @return array{
     *     status: 'ok'|'internal_blocked'|'collaborator_blocked',
     *     collaborator: Collaborator|null
     * }
     */
    private function resolveCollaboratorForContribution(array $collaboratorData): array
    {
        $collaborator = $this->collaboratorService->resolveOrCreateCollaborator($collaboratorData);
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
     */
    private function createItemWithImages(
        array $itemData,
        Collaborator $collaborator,
        ?UploadedFile $coverImage,
        array $galleryImages
    ): Item {
        $item = $this->createItemForContribution($itemData, $collaborator);
        if ($coverImage !== null) {
            $this->itemImagesService->storeCoverImage($item, $coverImage);
        }
        $this->itemImagesService->storeGalleryImages($item, $galleryImages);

        return $item;
    }

    /**
     * Create an item for contribution flow (transaction with identification code).
     *
     * @param  array<string, mixed>  $itemData
     */
    private function createItemForContribution(array $itemData, Collaborator $collaborator): Item
    {
        $itemData['collaborator_id'] = $collaborator->id;
        $itemData['identification_code'] = '000';

        return DB::transaction(function () use ($itemData): Item {
            $item = Item::create($itemData);
            $item->update([
                'identification_code' => $this->itemService->createIdentificationCode($item),
            ]);

            return $item;
        });
    }

    /**
     * Attach components using the validated ComponentRequest payload: each entry's
     * `item_id` is the catalog item that acts as the component (stored as `component_id`).
     *
     * @param  array<int, array<string, mixed>>  $componentsData
     */
    private function attachComponentsToItem(Item $item, array $componentsData): void
    {
        foreach ($componentsData as $componentItemData) {
            $componentId = (int) ($componentItemData['item_id'] ?? 0);
            if ($componentId <= 0) {
                continue;
            }

            if (! Item::query()->whereKey($componentId)->exists()) {
                logger()->info('Component item not found for contribution', [
                    'item_id' => $item->id,
                    'component_item_id' => $componentId,
                ]);
                continue;
            }

            ItemComponent::create([
                'item_id' => $item->id,
                'component_id' => $componentId,
                'validation' => (int) ($componentItemData['validation'] ?? 0),
            ]);
        }
    }
}
