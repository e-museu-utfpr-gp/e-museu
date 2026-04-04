<?php

namespace App\Http\Controllers\Admin\Catalog;

use App\Http\Controllers\Admin\AdminBaseController;
use App\Models\Language;
use App\Services\Collaborator\CollaboratorService;
use App\Services\Identity\LockService;
use Illuminate\Support\Arr;
use Illuminate\View\View;
use App\Http\Requests\Admin\Catalog\{AdminStoreItemRequest, AdminUpdateItemRequest};
use App\Models\Catalog\{Item, ItemImage};
use App\Services\Catalog\{ItemCategoryService, ItemImagesService, ItemService};
use App\Support\Admin\{AdminEditHeadingLocale, AdminIndexTableView};
use Illuminate\Http\{JsonResponse, RedirectResponse, Request};

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

    public function byItemCategory(Request $request, ItemService $itemService): JsonResponse
    {
        $itemCategoryId = (string) ($request->input('item_category') ?? '');
        $items = $itemService->getItemsByItemCategoryForAdminSelect($itemCategoryId);

        return response()->json($items);
    }

    public function show(Item $item): View
    {
        $item->load(Item::eagerLoadRelationsForAdminShow());

        return view('pages.admin.catalog.items.show', compact('item'));
    }

    public function create(ItemCategoryService $itemCategoryService, CollaboratorService $collaboratorService): View
    {
        return view('pages.admin.catalog.items.create', [
            'itemCategories' => $itemCategoryService->getForForm(),
            'collaborators' => $collaboratorService->getForForm(),
            'contentLanguages' => Language::forCatalogContentForms(),
            'preferredContentTabLanguageId' => AdminEditHeadingLocale::preferredContentTabLanguageId(),
        ]);
    }

    public function store(
        AdminStoreItemRequest $request,
        ItemService $itemService,
        ItemImagesService $itemImagesService
    ): RedirectResponse {
        $item = $itemService->createItemWithIdentificationCode($request);
        $itemImagesService->storeImagesFromStoreRequest($item, $request);

        return redirect()
            ->route('admin.catalog.items.show', $item->id)
            ->with('success', __('app.catalog.item.created'));
    }

    public function edit(
        Item $item,
        ItemCategoryService $itemCategoryService,
        CollaboratorService $collaboratorService,
        LockService $lockService,
        AdminEditHeadingLocale $headingLocale
    ): View {
        $item->load(['images', 'translations.language']);
        $lockService->requireUnlockedThenLock($item);

        return view('pages.admin.catalog.items.edit', array_merge([
            'item' => $item,
            'contentLanguages' => Language::forCatalogContentForms(),
            'itemCategories' => $itemCategoryService->getForForm(),
            'collaborators' => $collaboratorService->getForForm(),
        ], $headingLocale->resolveFor($item)));
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
        // Same as contribution: validated() skips absent keys; cleared `<input type="date">`
        // is often omitted from POST, so we must persist null explicitly.
        if (! array_key_exists('date', $data)) {
            $data['date'] = $request->input('date');
        }

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

        return redirect()
            ->route('admin.catalog.items.edit', $item)
            ->with('success', __('app.catalog.item_image.deleted'));
    }
}
