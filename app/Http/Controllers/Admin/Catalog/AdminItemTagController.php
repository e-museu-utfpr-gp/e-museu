<?php

namespace App\Http\Controllers\Admin\Catalog;

use App\Http\Controllers\Admin\AdminBaseController;
use App\Http\Requests\Admin\Catalog\AdminItemTagRequest;
use App\Models\Catalog\ItemTag;
use App\Services\Catalog\ItemCategoryService;
use App\Services\Catalog\ItemTagService;
use App\Support\Admin\AdminIndexTableView;
use App\Services\Taxonomy\TagCategoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminItemTagController extends AdminBaseController
{
    public function index(Request $request, ItemTagService $itemTagService): View
    {
        $result = $itemTagService->getPaginatedItemTagsForAdminIndex($request);

        return view('pages.admin.catalog.item-tags.index', array_merge([
            'itemTags' => $result['itemTags'],
            'count' => $result['count'],
        ], AdminIndexTableView::catalogItemTags()));
    }

    public function show(ItemTag $itemTag): View
    {
        return view('pages.admin.catalog.item-tags.show', compact('itemTag'));
    }

    public function create(
        ItemCategoryService $itemCategoryService,
        TagCategoryService $tagCategoryService
    ): View {
        return view('pages.admin.catalog.item-tags.create', [
            'itemCategories' => $itemCategoryService->getForForm(),
            'categories' => $tagCategoryService->getForIndex(),
        ]);
    }

    public function store(AdminItemTagRequest $request, ItemTagService $itemTagService): RedirectResponse
    {
        $itemTag = $itemTagService->createItemTag($request->validated());

        return redirect()->route('admin.catalog.item-tags.show', $itemTag)->with('success', __('app.catalog.itemtag.created'));
    }

    public function update(ItemTag $itemTag, ItemTagService $itemTagService): RedirectResponse
    {
        $itemTagService->updateItemTag($itemTag, [
            'validation' => ! $itemTag->validation,
        ]);

        return redirect()->route('admin.catalog.item-tags.show', $itemTag)->with('success', __('app.catalog.itemtag.updated'));
    }

    public function destroy(ItemTag $itemTag, ItemTagService $itemTagService): RedirectResponse
    {
        $itemTagService->deleteItemTag($itemTag);

        return redirect()->route('admin.catalog.item-tags.index')->with('success', __('app.catalog.itemtag.deleted'));
    }
}
