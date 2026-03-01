<?php

namespace App\Http\Controllers\Admin\Catalog;

use App\Http\Controllers\Admin\AdminBaseController;
use App\Http\Controllers\Admin\Concerns\BuildsAdminIndexQuery;
use App\Http\Requests\Catalog\ItemTagRequest;
use App\Models\Catalog\ItemCategory;
use App\Models\Catalog\ItemTag;
use App\Models\Taxonomy\TagCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminItemTagController extends AdminBaseController
{
    use BuildsAdminIndexQuery;

    /** @var array{baseTable: string, searchSpecial: array<string, array{table: string, column: string}>, sortSpecial: array<string, string>} */
    private const INDEX_CONFIG = [
        'baseTable' => 'item_tag',
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
        $count = ItemTag::count();
        $query = ItemTag::query();
        $query->leftJoin('items', 'item_tag.item_id', '=', 'items.id');
        $query->leftJoin('tags', 'item_tag.tag_id', '=', 'tags.id');
        $query->select([
            'item_tag.*',
            'item_tag.created_at AS item_tag_created',
            'item_tag.updated_at AS item_tag_updated',
            'item_tag.validation AS item_tag_validation',
            'items.name AS item_name',
            'tags.name AS tag_name',
        ]);

        $this->applyIndexSearch($query, $request->search_column, $request->search, self::INDEX_CONFIG);
        $this->applyIndexSort($query, $request->sort, $request->order, self::INDEX_CONFIG);

        $itemTags = $query->paginate(50)->withQueryString();

        return view('admin.catalog.item-tags.index', compact('itemTags', 'count'));
    }

    public function show(string $id): View
    {
        $itemTag = ItemTag::findOrFail($id);

        return view('admin.catalog.item-tags.show', compact('itemTag'));
    }

    public function create(): View
    {
        $categories = TagCategory::orderBy('name', 'asc')->get();
        $sections = ItemCategory::orderBy('name', 'asc')->get();

        return view('admin.catalog.item-tags.create', compact('categories', 'sections'));
    }

    public function store(ItemTagRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $itemTag = ItemTag::create($data);

        return redirect()->route('admin.item-tags.show', $itemTag)->with('success', __('app.catalog.itemtag.created'));
    }

    public function update(Request $request, ItemTag $itemTag): RedirectResponse
    {
        $itemTag->update([
            'validation' => ! $itemTag->validation,
        ]);

        return redirect()->route('admin.item-tags.show', $itemTag)->with('success', __('app.catalog.itemtag.updated'));
    }

    public function destroy(ItemTag $itemTag): RedirectResponse
    {
        $itemTag->delete();

        return redirect()->route('admin.item-tags.index')->with('success', __('app.catalog.itemtag.deleted'));
    }
}
