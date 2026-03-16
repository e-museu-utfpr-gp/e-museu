<?php

namespace App\Http\Controllers\Admin\Catalog;

use App\Http\Controllers\Admin\AdminBaseController;
use App\Http\Requests\Admin\Catalog\AdminItemCategoryRequest;
use App\Models\Catalog\ItemCategory;
use App\Services\Catalog\ItemCategoryService;
use App\Services\Identity\LockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminItemCategoryController extends AdminBaseController
{
    public function index(Request $request, ItemCategoryService $itemCategoryService): View
    {
        $result = $itemCategoryService->getPaginatedItemCategoriesForAdminIndex($request);

        return view('admin.catalog.item-categories.index', [
            'itemCategories' => $result['itemCategories'],
            'count' => $result['count'],
        ]);
    }

    public function show(ItemCategory $itemCategory): View
    {
        return view('admin.catalog.item-categories.show', compact('itemCategory'));
    }

    public function create(): View
    {
        return view('admin.catalog.item-categories.create');
    }

    public function store(AdminItemCategoryRequest $request, ItemCategoryService $itemCategoryService): RedirectResponse
    {
        $itemCategory = $itemCategoryService->createItemCategory($request->validated());

        return redirect()
            ->route('admin.item-categories.show', $itemCategory)
            ->with('success', __('app.catalog.item_category.created'));
    }

    public function edit(ItemCategory $itemCategory, LockService $lockService): View
    {
        $lockService->requireUnlocked($itemCategory);
        $lockService->lock($itemCategory);

        return view('admin.catalog.item-categories.edit', compact('itemCategory'));
    }

    public function update(
        AdminItemCategoryRequest $request,
        ItemCategory $itemCategory,
        ItemCategoryService $itemCategoryService,
        LockService $lockService
    ): RedirectResponse {
        $lockService->requireUnlocked($itemCategory);

        $itemCategoryService->updateItemCategory($itemCategory, $request->validated());

        $lockService->unlock($itemCategory);

        return redirect()
            ->route('admin.item-categories.show', $itemCategory)
            ->with('success', __('app.catalog.item_category.updated'));
    }

    public function destroy(
        ItemCategory $itemCategory,
        ItemCategoryService $itemCategoryService,
        LockService $lockService
    ): RedirectResponse {
        $lockService->requireUnlocked($itemCategory);

        $lockService->unlock($itemCategory);
        $itemCategoryService->deleteItemCategory($itemCategory);

        return redirect()
            ->route('admin.item-categories.index')
            ->with('success', __('app.catalog.item_category.deleted'));
    }
}
