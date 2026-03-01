<?php

namespace App\Http\Controllers\Admin\Catalog;

use App\Http\Controllers\Admin\AdminBaseController;
use App\Http\Controllers\Admin\Concerns\LocksSubject;
use App\Http\Requests\Catalog\ItemCategoryRequest;
use App\Models\Catalog\ItemCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminItemCategoryController extends AdminBaseController
{
    use LocksSubject;

    public function index(Request $request): View
    {
        $query = ItemCategory::query();
        $count = ItemCategory::count();

        if ($request->search_column && $request->search) {
            $query->where($request->search_column, 'LIKE', "%{$request->search}%");
        }

        if ($request->sort && $request->order) {
            $query->orderBy($request->sort, $request->order);
        }

        $itemCategories = $query->paginate(50)->withQueryString();

        return view('admin.catalog.item-categories.index', compact('itemCategories', 'count'));
    }

    public function show(string $id): View
    {
        $itemCategory = ItemCategory::findOrFail($id);

        return view('admin.catalog.item-categories.show', compact('itemCategory'));
    }

    public function create(): View
    {
        return view('admin.catalog.item-categories.create');
    }

    public function store(ItemCategoryRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $itemCategory = ItemCategory::create($data);

        return redirect()
            ->route('admin.item-categories.show', $itemCategory)
            ->with('success', __('app.catalog.item_category.created'));
    }

    public function edit(string $id): View
    {
        $itemCategory = ItemCategory::findOrFail($id);
        $this->requireUnlocked($itemCategory);

        $this->lock($itemCategory);

        return view('admin.catalog.item-categories.edit', compact('itemCategory'));
    }

    public function update(ItemCategoryRequest $request, ItemCategory $itemCategory): RedirectResponse
    {
        $this->requireUnlocked($itemCategory);

        $data = $request->validated();

        $itemCategory->update($data);

        $this->unlock($itemCategory);

        return redirect()
            ->route('admin.item-categories.show', $itemCategory)
            ->with('success', __('app.catalog.item_category.updated'));
    }

    public function destroy(ItemCategory $itemCategory): RedirectResponse
    {
        $this->requireUnlocked($itemCategory);

        $this->unlock($itemCategory);

        $itemCategory->delete();

        return redirect()
            ->route('admin.item-categories.index')
            ->with('success', __('app.catalog.item_category.deleted'));
    }
}
