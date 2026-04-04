<?php

namespace App\Http\Controllers\Admin\Catalog;

use App\Http\Controllers\Admin\AdminBaseController;
use App\Http\Requests\Admin\Catalog\AdminItemCategoryRequest;
use App\Models\Catalog\ItemCategory;
use App\Models\Language;
use App\Services\Catalog\ItemCategoryService;
use App\Services\Identity\LockService;
use App\Support\Admin\AdminEditHeadingLocale;
use App\Support\Admin\AdminIndexTableView;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminItemCategoryController extends AdminBaseController
{
    public function index(Request $request, ItemCategoryService $itemCategoryService): View
    {
        $result = $itemCategoryService->getPaginatedItemCategoriesForAdminIndex($request);

        return view('pages.admin.catalog.item-categories.index', array_merge([
            'itemCategories' => $result['itemCategories'],
            'count' => $result['count'],
        ], AdminIndexTableView::catalogItemCategories()));
    }

    public function show(ItemCategory $itemCategory): View
    {
        return view('pages.admin.catalog.item-categories.show', compact('itemCategory'));
    }

    public function create(): View
    {
        return view('pages.admin.catalog.item-categories.create', [
            'contentLanguages' => Language::forAdminContentForms(),
            'preferredContentTabLanguageId' => AdminEditHeadingLocale::preferredContentTabLanguageId(),
        ]);
    }

    public function store(AdminItemCategoryRequest $request, ItemCategoryService $itemCategoryService): RedirectResponse
    {
        $itemCategory = $itemCategoryService->createItemCategory($request->validated());

        return redirect()
            ->route('admin.catalog.item-categories.show', $itemCategory)
            ->with('success', __('app.catalog.item_category.created'));
    }

    public function edit(
        ItemCategory $itemCategory,
        LockService $lockService,
        AdminEditHeadingLocale $headingLocale
    ): View {
        $lockService->requireUnlockedThenLock($itemCategory);

        return view('pages.admin.catalog.item-categories.edit', array_merge([
            'itemCategory' => $itemCategory,
            'contentLanguages' => Language::forAdminContentForms(),
        ], $headingLocale->resolveFor($itemCategory)));
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
            ->route('admin.catalog.item-categories.show', $itemCategory)
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
            ->route('admin.catalog.item-categories.index')
            ->with('success', __('app.catalog.item_category.deleted'));
    }
}
