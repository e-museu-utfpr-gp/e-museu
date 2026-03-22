<?php

namespace App\Http\Controllers\Admin\Catalog;

use App\Http\Controllers\Admin\AdminBaseController;
use App\Http\Requests\Admin\Catalog\AdminStoreItemRequest;
use App\Http\Requests\Admin\Catalog\AdminUpdateItemRequest;
use App\Models\Catalog\Item;
use App\Models\Catalog\ItemImage;
use App\Services\Catalog\ItemCategoryService;
use App\Services\Catalog\ItemImagesService;
use App\Services\Catalog\ItemService;
use App\Services\Collaborator\CollaboratorService;
use App\Support\Admin\AdminIndexTableView;
use App\Services\Identity\LockService;
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
    public function index(Request $request, ItemService $itemService): View
    {
        $result = $itemService->getPaginatedItemsForAdminIndex($request);

        return view('pages.admin.catalog.items.index', array_merge([
            'items' => $result['items'],
            'count' => $result['count'],
        ], AdminIndexTableView::catalogItems()));
    }

    public function show(Item $item): View
    {
        $item->load('images');

        return view('pages.admin.catalog.items.show', compact('item'));
    }

    public function create(ItemCategoryService $itemCategoryService, CollaboratorService $collaboratorService): View
    {
        return view('pages.admin.catalog.items.create', [
            'itemCategories' => $itemCategoryService->getForForm(),
            'collaborators' => $collaboratorService->getForForm(),
        ]);
    }

    public function store(
        AdminStoreItemRequest $request,
        ItemService $itemService,
        ItemImagesService $itemImagesService
    ): RedirectResponse {
        $item = $itemService->createItemWithIdentificationCode($request);
        $itemImagesService->storeImagesFromStoreRequest($item, $request);

        return redirect()->route('admin.catalog.items.show', $item->id)->with('success', __('app.catalog.item.created'));
    }

    public function edit(
        Item $item,
        ItemCategoryService $itemCategoryService,
        CollaboratorService $collaboratorService,
        LockService $lockService
    ): View {
        $item->load(['images', 'coverImage']);
        $lockService->requireUnlocked($item);
        $lockService->lock($item);

        return view('pages.admin.catalog.items.edit', [
            'item' => $item,
            'itemCategories' => $itemCategoryService->getForForm(),
            'collaborators' => $collaboratorService->getForForm(),
        ]);
    }

    public function update(
        AdminUpdateItemRequest $request,
        Item $item,
        ItemImagesService $itemImagesService,
        ItemService $itemService,
        LockService $lockService
    ): RedirectResponse {
        $lockService->requireUnlocked($item);

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

        $lockService->unlock($item);

        return redirect()->route('admin.catalog.items.show', $item)->with('success', __('app.catalog.item.updated'));
    }

    public function destroy(
        Item $item,
        ItemImagesService $itemImagesService,
        ItemService $itemService,
        LockService $lockService
    ): RedirectResponse {
        $lockService->requireUnlocked($item);

        $lockService->unlock($item);

        $itemImagesService->deleteAllImagesForItem($item);
        $itemService->deleteItem($item);

        return redirect()->route('admin.catalog.items.index')->with('success', __('app.catalog.item.deleted'));
    }

    public function destroyImage(
        Item $item,
        ItemImage $image,
        ItemImagesService $itemImagesService,
        LockService $lockService
    ): RedirectResponse {
        $lockService->requireUnlocked($item);
        $itemImagesService->deleteImage($item, $image);

        return redirect()->route('admin.catalog.items.edit', $item)->with('success', __('app.catalog.item_image.deleted'));
    }
}
