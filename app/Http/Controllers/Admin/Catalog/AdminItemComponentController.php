<?php

namespace App\Http\Controllers\Admin\Catalog;

use App\Http\Controllers\Admin\AdminBaseController;
use App\Http\Requests\Admin\Catalog\AdminSingleComponentRequest;
use App\Models\Catalog\ItemComponent;
use App\Services\Catalog\ItemCategoryService;
use App\Services\Catalog\ItemComponentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminItemComponentController extends AdminBaseController
{
    public function index(Request $request, ItemComponentService $itemComponentService): View
    {
        $result = $itemComponentService->getPaginatedItemComponentsForAdminIndex($request);

        return view('admin.catalog.item-components.index', [
            'itemComponents' => $result['itemComponents'],
            'count' => $result['count'],
        ]);
    }

    public function show(ItemComponent $itemComponent): View
    {
        return view('admin.catalog.item-components.show', compact('itemComponent'));
    }

    public function create(ItemCategoryService $itemCategoryService): View
    {
        return view('admin.catalog.item-components.create', [
            'itemCategories' => $itemCategoryService->getForForm(),
        ]);
    }

    public function store(
        AdminSingleComponentRequest $request,
        ItemComponentService $itemComponentService
    ): RedirectResponse {
        $itemComponent = $itemComponentService->createItemComponent($request->validated());

        return redirect()
            ->route('admin.item-components.show', $itemComponent)
            ->with('success', __('app.catalog.component.created'));
    }

    public function update(ItemComponent $itemComponent, ItemComponentService $itemComponentService): RedirectResponse
    {
        $itemComponentService->updateItemComponent($itemComponent, [
            'validation' => ! $itemComponent->validation,
        ]);

        return redirect()
            ->route('admin.item-components.show', $itemComponent)
            ->with('success', __('app.catalog.component.updated'));
    }

    public function destroy(ItemComponent $itemComponent, ItemComponentService $itemComponentService): RedirectResponse
    {
        $itemComponentService->deleteItemComponent($itemComponent);

        return redirect()->route('admin.item-components.index')->with('success', __('app.catalog.component.deleted'));
    }
}
