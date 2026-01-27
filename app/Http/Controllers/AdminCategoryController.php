<?php

namespace App\Http\Controllers;

use App\Http\Middleware\CheckLock;
use Illuminate\Http\Request;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AdminCategoryController extends AdminBaseController
{
    public function __construct()
    {
        $this->middleware(CheckLock::class)->only(['edit', 'update', 'destroy']);
    }

    public function index(Request $request): View
    {
        $query = Category::query();
        $count = Category::count();

        if ($request->search_column && $request->search) {
            $query->where($request->search_column, 'LIKE', "%{$request->search}%");
        }

        if ($request->sort && $request->order) {
            if ($request->order === 'asc') {
                $query->orderBy($request->sort, 'desc');
            } else {
                $query->orderBy($request->sort, 'asc');
            }
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
        $category = Category::find($id);

        return view('admin.categories.show', compact('category'));
    }

    public function edit(string $id): View
    {
        $category = Category::findOrFail($id);

        $this->lock($category);

        return view('admin.categories.edit', compact('category'));
    }

    public function update(CategoryRequest $request, Category $category): RedirectResponse
    {
        $data = $request->validated();

        $category->update($data);

        $this->unlock($category);

        $message = 'Categoria atualizada com sucesso.';

        return redirect()->route('admin.categories.show', $category)->with('success', $message);
    }

    public function destroy(Category $category): RedirectResponse
    {
        $this->unlock($category);

        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Categoria excluída com sucesso.');
    }
}
