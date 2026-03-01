<?php

namespace App\Http\Controllers\Admin\Catalog;

use App\Http\Controllers\Admin\AdminBaseController;
use App\Http\Controllers\Admin\Concerns\BuildsAdminIndexQuery;
use App\Http\Requests\Catalog\SingleComponentRequest;
use App\Models\Catalog\ItemComponent;
use App\Models\Catalog\ItemCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminItemComponentController extends AdminBaseController
{
    use BuildsAdminIndexQuery;

    /** @var array{baseTable: string, searchSpecial: array<string, array{table: string, column: string}>, sortSpecial: array<string, string>} */
    private const INDEX_CONFIG = [
        'baseTable' => 'item_component',
        'searchSpecial' => [
            'item_id' => ['table' => 'item', 'column' => 'name'],
            'component_id' => ['table' => 'component', 'column' => 'name'],
        ],
        'sortSpecial' => [
            'item_id' => 'item.name',
            'component_id' => 'component.name',
        ],
    ];

    public function index(Request $request): View
    {
        $count = ItemComponent::count();
        $query = ItemComponent::query();
        $query->leftJoin('items as item', 'item_component.item_id', '=', 'item.id');
        $query->leftJoin('items as component', 'item_component.component_id', '=', 'component.id');
        $query->select([
            'item_component.*',
            'item_component.created_at AS item_component_created',
            'item_component.updated_at AS item_component_updated',
            'item_component.validation AS item_component_validation',
            'item.name AS item_name',
            'component.name AS component_name',
        ]);

        $this->applyIndexSearch($query, $request->search_column, $request->search, self::INDEX_CONFIG);
        $this->applyIndexSort($query, $request->sort, $request->order, self::INDEX_CONFIG);

        $itemComponents = $query->paginate(30)->withQueryString();

        return view('admin.catalog.item-components.index', compact('itemComponents', 'count'));
    }

    public function show(string $id): View
    {
        $itemComponent = ItemComponent::findOrFail($id);

        return view('admin.catalog.item-components.show', compact('itemComponent'));
    }

    public function create(): View
    {
        $sections = ItemCategory::orderBy('name', 'asc')->get();

        return view('admin.catalog.item-components.create', compact('sections'));
    }

    public function store(SingleComponentRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $itemComponent = ItemComponent::create($data);

        return redirect()
            ->route('admin.item-components.show', $itemComponent)
            ->with('success', __('app.catalog.component.created'));
    }

    public function update(Request $request, ItemComponent $itemComponent): RedirectResponse
    {
        $itemComponent->update([
            'validation' => ! $itemComponent->validation,
        ]);

        return redirect()
            ->route('admin.item-components.show', $itemComponent)
            ->with('success', __('app.catalog.component.updated'));
    }

    public function destroy(ItemComponent $itemComponent): RedirectResponse
    {
        $itemComponent->delete();

        return redirect()->route('admin.item-components.index')->with('success', __('app.catalog.component.deleted'));
    }
}
