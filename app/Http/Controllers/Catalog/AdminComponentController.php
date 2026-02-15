<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\AdminBaseController;
use App\Http\Controllers\Concerns\BuildsAdminIndexQuery;
use Illuminate\Http\Request;
use App\Http\Requests\SingleComponentRequest;
use App\Models\Catalog\ItemComponent;
use App\Models\Catalog\Section;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AdminComponentController extends AdminBaseController
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

        $components = $query->paginate(30)->withQueryString();

        return view('admin.components.index', compact('components', 'count'));
    }

    public function show(string $id): View
    {
        $component = ItemComponent::find($id);

        return view('admin.components.show', compact('component'));
    }

    public function create(): View
    {
        $sections = Section::orderBy('name', 'asc')->get();

        return view('admin.components.create', compact('sections'));
    }

    public function store(SingleComponentRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $component = ItemComponent::create($data);

        $message = 'Componente adicionada com sucesso.';

        return redirect()->route('admin.components.show', $component)->with('success', $message);
    }

    public function edit(string $id): View
    {
        $sections = Section::orderBy('name', 'asc')->get();
        $component = ItemComponent::findOrFail($id);

        return view('admin.components.edit', compact('component', 'sections'));
    }

    public function update(Request $request, ItemComponent $component): RedirectResponse
    {
        $data = $request->all();

        if ($component->validation === true) {
            $data['validation'] = false;
        } else {
            $data['validation'] = true;
        }

        $component->update($data);

        $message = 'Componente atualizado com sucesso.';

        return redirect()->route('admin.components.show', $component)->with('success', $message);
    }

    public function destroy(ItemComponent $component): RedirectResponse
    {
        $component->delete();
        return redirect()->route('admin.components.index')->with('success', 'Componente excluído com sucesso.');
    }
}
