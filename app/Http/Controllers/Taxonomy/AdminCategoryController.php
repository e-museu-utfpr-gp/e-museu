<?php

namespace App\Http\Controllers\Taxonomy;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\LocksSubject;
use App\Http\Requests\Taxonomy\CategoryRequest;
use App\Models\Taxonomy\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminCategoryController extends Controller
{
    use LocksSubject;

    public function index(Request $request): View
    {
        $query = Category::query();
        $count = Category::count();

        if ($request->search_column && $request->search) {
            $query->where($request->search_column, 'LIKE', "%{$request->search}%");
        }

        if ($request->sort && $request->order) {
            $query->orderBy($request->sort, $request->order);
        }

        $categories = $query->paginate(30)->withQueryString();

        return view('admin.categories.index', compact('categories', 'count'));
    }

    public function create(): View
    {
        return view('admin.categories.create');
    }

    public function store(CategoryRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $category = Category::create($data);

        $message = 'Categoria adicionada com sucesso.';

        return redirect()->route('admin.categories.show', $category)->with('success', $message);
    }

    public function show(string $id): View
    {
        $category = Category::findOrFail($id);

        return view('admin.categories.show', compact('category'));
    }

    public function edit(string $id): View
    {
        $category = Category::findOrFail($id);
        $this->requireUnlocked($category);

        $this->lock($category);

        return view('admin.categories.edit', compact('category'));
    }

    public function update(CategoryRequest $request, Category $category): RedirectResponse
    {
        $this->requireUnlocked($category);

        $data = $request->validated();

        $category->update($data);

        $this->unlock($category);

        $message = 'Categoria atualizada com sucesso.';

        return redirect()->route('admin.categories.show', $category)->with('success', $message);
    }

    public function destroy(Category $category): RedirectResponse
    {
        $this->requireUnlocked($category);

        $this->unlock($category);

        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Categoria excluída com sucesso.');
    }
}
