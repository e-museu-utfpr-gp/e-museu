<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\BuildsAdminIndexQuery;
use Illuminate\Http\Request;
use App\Http\Requests\ItemTagRequest;
use App\Models\Catalog\Section;
use App\Models\Catalog\TagItem;
use App\Models\Taxonomy\Category;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AdminItemTagController extends AdminBaseController
{
    use BuildsAdminIndexQuery;

    /** @var array{baseTable: string, searchSpecial: array<string, array{table: string, column: string}>, sortSpecial: array<string, string>} */
    private const INDEX_CONFIG = [
        'baseTable' => 'tag_item',
        'searchSpecial' => [
            'item_id' => ['table' => 'items', 'column' => 'name'],
            'tag_id' => ['table' => 'tags', 'column' => 'name'],
        ],
        'sortSpecial' => [
            'item_id' => 'items.name',
            'tag_id' => 'tags.name',
        ],
    ];

    public function index(Request $request): View
    {
        $count = TagItem::count();
        $query = TagItem::query();
        $query->leftJoin('items', 'tag_item.item_id', '=', 'items.id');
        $query->leftJoin('tags', 'tag_item.tag_id', '=', 'tags.id');
        $query->select([
            'tag_item.*',
            'tag_item.created_at AS tag_item_created',
            'tag_item.updated_at AS tag_item_updated',
            'tag_item.validation AS tag_item_validation',
            'items.name AS item_name',
            'tags.name AS tag_name',
        ]);

        $this->applyIndexSearch($query, $request->search_column, $request->search, self::INDEX_CONFIG);
        $this->applyIndexSort($query, $request->sort, $request->order, self::INDEX_CONFIG);

        $itemTags = $query->paginate(50)->withQueryString();

        return view('admin.item-tags.index', compact('itemTags', 'count'));
    }

    public function show(string $id): View
    {
        $itemTag = TagItem::find($id);

        return view('admin.item-tags.show', compact('itemTag'));
    }

    public function create(): View
    {
        $categories = Category::orderBy('name', 'asc')->get();
        $sections = Section::orderBy('name', 'asc')->get();

        return view('admin.item-tags.create', compact('categories', 'sections'));
    }

    public function store(ItemTagRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $itemTag = TagItem::create($data);

        $message = 'Relacionamento adicionado com sucesso.';

        return redirect()->route('admin.item-tags.show', $itemTag)->with('success', $message);
    }

    public function edit(string $id): View
    {
        $categories = Category::orderBy('name', 'asc')->get();
        $sections = Section::orderBy('name', 'asc')->get();
        $itemTag = TagItem::findOrFail($id);

        return view('admin.item-tags.edit', compact('itemTag', 'categories', 'sections'));
    }

    public function update(Request $request, TagItem $itemTag): RedirectResponse
    {
        $data = $request->all();

        if ($itemTag->validation === true) {
            $data['validation'] = false;
        } else {
            $data['validation'] = true;
        }

        $itemTag->update($data);

        $message = 'Relacionamento atualizado com sucesso.';

        return redirect()->route('admin.item-tags.show', $itemTag)->with('success', $message);
    }

    public function destroy(TagItem $itemTag): RedirectResponse
    {
        $itemTag->delete();
        return redirect()->route('admin.item-tags.index')->with('success', 'Relacionamento excluído com sucesso.');
    }
}
