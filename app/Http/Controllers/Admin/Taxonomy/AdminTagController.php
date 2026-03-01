<?php

namespace App\Http\Controllers\Admin\Taxonomy;

use App\Http\Controllers\Admin\AdminBaseController;
use App\Http\Controllers\Admin\Concerns\BuildsAdminIndexQuery;
use App\Http\Controllers\Admin\Concerns\LocksSubject;
use App\Http\Requests\Catalog\SingleTagRequest;
use App\Models\Taxonomy\Category;
use App\Models\Taxonomy\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminTagController extends AdminBaseController
{
    use BuildsAdminIndexQuery;
    use LocksSubject;

    /** @var array{baseTable: string, searchSpecial: array<string, array{table: string, column: string}>, sortSpecial: array<string, string>} */
    private const INDEX_CONFIG = [
        'baseTable' => 'tags',
        'searchSpecial' => [
            'category_id' => ['table' => 'categories', 'column' => 'name'],
        ],
        'sortSpecial' => [
            'category_id' => 'categories.name',
        ],
    ];

    public function index(Request $request): View
    {
        $count = Tag::count();
        $query = Tag::query();
        $query->leftJoin('categories', 'tags.category_id', '=', 'categories.id');
        $query->select([
            'tags.*',
            'tags.name AS tag_name',
            'tags.created_at AS tag_created',
            'tags.updated_at AS tag_updated',
            'categories.name AS category_name',
        ]);

        $this->applyIndexSearch($query, $request->search_column, $request->search, self::INDEX_CONFIG);
        $this->applyIndexSort($query, $request->sort, $request->order, self::INDEX_CONFIG);

        $tags = $query->paginate(30)->withQueryString();

        return view('admin.taxonomy.tags.index', compact('tags', 'count'));
    }

    public function show(string $id): View
    {
        $tag = Tag::findOrFail($id);

        return view('admin.taxonomy.tags.show', compact('tag'));
    }

    public function create(): View
    {
        $categories = Category::orderBy('name', 'asc')->get();

        return view('admin.taxonomy.tags.create', compact('categories'));
    }

    public function store(SingleTagRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $tag = Tag::create($data);

        return redirect()->route('admin.tags.show', $tag)->with('success', __('app.taxonomy.tag.created'));
    }

    public function edit(string $id): View
    {
        $tag = Tag::findOrFail($id);
        $this->requireUnlocked($tag);

        $categories = Category::orderBy('name', 'asc')->get();

        $this->lock($tag);

        return view('admin.taxonomy.tags.edit', compact('tag', 'categories'));
    }

    public function update(SingleTagRequest $request, Tag $tag): RedirectResponse
    {
        $this->requireUnlocked($tag);

        $data = $request->validated();

        $tag->update($data);

        $this->unlock($tag);

        return redirect()->route('admin.tags.show', $tag)->with('success', __('app.taxonomy.tag.updated'));
    }

    public function destroy(Tag $tag): RedirectResponse
    {
        $this->requireUnlocked($tag);

        $this->unlock($tag);

        $tag->delete();

        return redirect()->route('admin.tags.index')->with('success', __('app.taxonomy.tag.deleted'));
    }
}
