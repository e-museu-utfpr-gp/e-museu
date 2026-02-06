<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\BuildsAdminIndexQuery;
use App\Http\Middleware\CheckLock;
use Illuminate\Http\Request;
use App\Http\Requests\SingleTagRequest;
use App\Models\Tag;
use App\Models\Category;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AdminTagController extends AdminBaseController
{
    use BuildsAdminIndexQuery;

    public function __construct()
    {
        $this->middleware(CheckLock::class)->only(['edit', 'update', 'destroy']);
    }

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

        return view('admin.tags.index', compact('tags', 'count'));
    }

    public function show(string $id): View
    {
        $tag = Tag::find($id);

        return view('admin.tags.show', compact('tag'));
    }

    public function create(): View
    {
        $categories = Category::orderBy('name', 'asc')->get();

        return view('admin.tags.create', compact('categories'));
    }

    public function store(SingleTagRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $tag = Tag::create($data);

        return redirect()->route('admin.tags.show', $tag)->with('success', 'Etiqueta adicionada com sucesso.');
    }

    public function edit(string $id): View
    {
        $tag = Tag::findOrFail($id);

        $categories = Category::orderBy('name', 'asc')->get();

        $this->lock($tag);

        return view('admin.tags.edit', compact('tag', 'categories'));
    }

    public function update(SingleTagRequest $request, Tag $tag): RedirectResponse
    {
        $data = $request->validated();

        $tag->update($data);

        $this->unlock($tag);

        return redirect()->route('admin.tags.show', $tag)->with('success', 'Etiqueta atualizada com sucesso.');
    }

    public function destroy(Tag $tag): RedirectResponse
    {
        $this->unlock($tag);

        $tag->delete();

        return redirect()->route('admin.tags.index')->with('success', 'Etiqueta excluída com sucesso.');
    }
}
