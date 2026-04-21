<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Catalog\Item;

use App\Actions\Catalog\UpdateAdminItemFromRequestAction;
use App\Http\Controllers\Admin\AdminBaseController;
use App\Http\Requests\Admin\Catalog\{AdminStoreItemRequest, AdminUpdateItemRequest};
use App\Models\Catalog\Item;
use App\Services\Catalog\{AdminItemAdminViewDataService, ItemImagesService, ItemQrCodeService, ItemService};
use App\Services\Identity\LockService;
use Illuminate\Http\{JsonResponse, RedirectResponse, Request};
use Illuminate\View\View;
use Throwable;

class AdminItemController extends AdminBaseController
{
    public function index(Request $request, AdminItemAdminViewDataService $viewData): View
    {
        return view('pages.admin.catalog.items.index', $viewData->forIndex($request));
    }

    public function byItemCategory(Request $request, ItemService $itemService): JsonResponse
    {
        $itemCategoryId = (string) ($request->input('item_category') ?? '');
        $items = $itemService->getItemsByItemCategoryForAdminSelect($itemCategoryId);

        return response()->json($itemService->mapItemsToCategorySelectJson($items));
    }

    public function show(Item $item, AdminItemAdminViewDataService $viewData): View
    {
        return view('pages.admin.catalog.items.show', $viewData->forShow($item));
    }

    public function create(AdminItemAdminViewDataService $viewData): View
    {
        return view('pages.admin.catalog.items.create', $viewData->forCreate());
    }

    public function store(
        AdminStoreItemRequest $request,
        ItemService $itemService,
        ItemImagesService $itemImagesService,
        ItemQrCodeService $itemQrCodeService
    ): RedirectResponse {
        $item = $itemService->createItemWithIdentificationCode($request);
        $itemImagesService->storeImagesFromStoreRequest($item, $request);
        try {
            $itemQrCodeService->regenerateForItem($item);
        } catch (Throwable $e) {
            report($e);
        }

        return redirect()
            ->route('admin.catalog.items.show', $item->id)
            ->with('success', __('app.catalog.item.created'));
    }

    public function edit(Item $item, LockService $lockService, AdminItemAdminViewDataService $viewData): View
    {
        $lockService->requireUnlockedThenLock($item);

        return view('pages.admin.catalog.items.edit', $viewData->forEdit($item));
    }

    public function update(
        AdminUpdateItemRequest $request,
        Item $item,
        UpdateAdminItemFromRequestAction $updateAdminItem,
        LockService $lockService
    ): RedirectResponse {
        $lockService->requireUnlocked($item);

        $updateAdminItem->handle($item, $request);

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
}
