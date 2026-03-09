<?php

namespace App\Http\Controllers\Admin\Catalog;

use App\Http\Controllers\Admin\AdminBaseController;
use App\Http\Controllers\Admin\Concerns\LocksSubject;
use App\Http\Requests\Admin\Catalog\AdminStoreItemRequest;
use App\Http\Requests\Admin\Catalog\AdminUpdateItemRequest;
use App\Models\Catalog\Item;
use App\Models\Catalog\ItemImage;
use App\Services\Catalog\ItemImagesService;
use App\Services\Catalog\ItemContributionService;
use App\Services\Catalog\ItemService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\View\View;

/**
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AdminItemController extends AdminBaseController
{
    use LocksSubject;

    public function index(Request $request, ItemService $itemService): View
    {
        $result = $itemService->getPaginatedItemsForAdminIndex($request);

        return view('admin.catalog.items.index', [
            'items' => $result['items'],
            'count' => $result['count'],
        ]);
    }

    public function show(Item $item): View
    {
        $item->load('images');

        return view('admin.catalog.items.show', compact('item'));
    }

    public function create(ItemService $itemService): View
    {
        $data = $itemService->getSectionsAndCollaborators();

        return view('admin.catalog.items.create', [
            'sections' => $data['sections'],
            'collaborators' => $data['collaborators'],
        ]);
    }

    public function store(
        AdminStoreItemRequest $request,
        ItemService $itemService,
        ItemContributionService $itemContributionService,
        ItemImagesService $itemImagesService
    ): RedirectResponse {
        $item = $itemService->createItemWithIdentificationCode($request, $itemContributionService);
        $itemImagesService->storeImagesFromStoreRequest($item, $request);

        return redirect()->route('admin.items.show', $item->id)->with('success', __('app.catalog.item.created'));
    }

    public function edit(Item $item, ItemService $itemService): View
    {
        $item->load(['images', 'coverImage']);
        $this->requireUnlocked($item);

        $this->lock($item);

        $data = $itemService->getSectionsAndCollaborators();

        return view('admin.catalog.items.edit', [
            'item' => $item,
            'sections' => $data['sections'],
            'collaborators' => $data['collaborators'],
        ]);
    }

    public function update(
        AdminUpdateItemRequest $request,
        Item $item,
        ItemImagesService $itemImagesService,
        ItemService $itemService
    ): RedirectResponse {
        $this->requireUnlocked($item);

        $data = Arr::except($request->validated(), [
            'image',
            'gallery_images',
            'delete_image_ids',
            'set_cover_image_id',
        ]);

        $itemImagesService->processDeleteImageIds($item, $request);
        $itemImagesService->processCoverImage($item, $request);
        $itemImagesService->processGalleryImages($item, $request);

        $itemService->updateItem($item, $data);

        $this->unlock($item);

        return redirect()->route('admin.items.show', $item)->with('success', __('app.catalog.item.updated'));
    }

    public function destroy(
        Item $item,
        ItemImagesService $itemImagesService,
        ItemService $itemService
    ): RedirectResponse {
        $this->requireUnlocked($item);

        $this->unlock($item);

        $itemImagesService->deleteAllImagesForItem($item);
        $itemService->deleteItem($item);

        return redirect()->route('admin.items.index')->with('success', __('app.catalog.item.deleted'));
    }

    public function destroyImage(
        Item $item,
        ItemImage $image,
        ItemImagesService $itemImagesService
    ): RedirectResponse {
        $this->requireUnlocked($item);
        $itemImagesService->deleteImage($item, $image);

        return redirect()->route('admin.items.edit', $item)->with('success', __('app.catalog.item_image.deleted'));
    }
}
