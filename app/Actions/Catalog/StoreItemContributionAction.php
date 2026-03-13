<?php

namespace App\Actions\Catalog;

use App\Enums\Collaborator\CollaboratorRole;
use App\Models\Catalog\Item;
use App\Models\Catalog\ItemComponent;
use App\Models\Collaborator\Collaborator;
use App\Services\Catalog\ItemImagesService;
use App\Services\Catalog\ItemService;
use App\Services\Catalog\ItemTagService;
use App\Services\Catalog\ExtraService;
use App\Services\Collaborator\CollaboratorService;
use App\Services\Taxonomy\TagService;
use Illuminate\Http\RedirectResponse;
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
     * Resolves collaborator, creates item, images, tags, extras and components.
     *
     * @param  array<string, mixed>                 $collaboratorData
     * @param  array<string, mixed>                 $itemData
     * @param  array<int, array<string, mixed>>     $tags
     * @param  array<int, array<string, mixed>>     $extras
     * @param  array<int, array<string, mixed>>     $components
     * @param  array<int, UploadedFile>|null        $galleryImages
     */
    public function handle(
        array $collaboratorData,
        array $itemData,
        array $tags,
        array $extras,
        array $components,
        ?UploadedFile $coverImage,
        ?array $galleryImages = null
    ): RedirectResponse {
        $collaborator = $this->resolveCollaboratorForContribution($collaboratorData);
        if ($collaborator instanceof RedirectResponse) {
            return $collaborator;
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

        return redirect()->route('items.create')->with('success', __('app.catalog.item.contribution_success'));
    }

    /**
     * Resolve collaborator for contribution; return redirect with errors if not allowed.
     *
     * @param  array<string, mixed>  $collaboratorData
     * @return Collaborator|RedirectResponse
     */
    private function resolveCollaboratorForContribution(array $collaboratorData): Collaborator|RedirectResponse
    {
        $collaborator = $this->collaboratorService->resolveOrCreateCollaborator($collaboratorData);
        if ($collaborator->role === CollaboratorRole::INTERNAL) {
            return back()->withErrors(['contact' => __('app.collaborator.contact_reserved_for_internal')]);
        }
        if ($collaborator->blocked === true) {
            return back()->withErrors(['blocked' => __('app.collaborator.blocked_from_registering')]);
        }

        return $collaborator;
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
        $item->normalizeSingleCover();

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
            $updateData = $itemData;
            $updateData['identification_code'] = $this->itemService->createIdentificationCode($item);
            $item->update($updateData);

            return $item;
        });
    }

    /**
     * Attach components to an item (create ItemComponent records for each component found by category+name).
     *
     * @param  array<int, array<string, mixed>>  $componentsData
     */
    private function attachComponentsToItem(Item $item, array $componentsData): void
    {
        foreach ($componentsData as $componentItemData) {
            $component = Item::where('category_id', '=', $componentItemData['category_id'])
                ->where('name', '=', $componentItemData['name'])
                ->first();
            if (! $component) {
                continue;
            }
            $componentItemData['component_id'] = $component->id;
            $componentItemData['item_id'] = $item->id;
            ItemComponent::create($componentItemData);
        }
    }
}
