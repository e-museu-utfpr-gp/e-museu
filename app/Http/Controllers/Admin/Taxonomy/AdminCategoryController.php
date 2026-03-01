<?php

namespace App\Http\Controllers\Admin\Taxonomy;

use App\Http\Controllers\Admin\AdminBaseController;
use App\Http\Controllers\Admin\Concerns\LocksSubject;
use App\Http\Requests\Taxonomy\CategoryRequest;
use App\Models\Taxonomy\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminCategoryController extends AdminBaseController
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

        return view('admin.taxonomy.categories.index', compact('categories', 'count'));
    }

    public function create(): View
    {
        return view('admin.taxonomy.categories.create');
    }

    public function store(CategoryRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $category = Category::create($data);

        return redirect()
            ->route('admin.categories.show', $category)
            ->with('success', __('app.taxonomy.category.created'));
    }

    public function show(string $id): View
    {
        $category = Category::findOrFail($id);

        return view('admin.taxonomy.categories.show', compact('category'));
    }

    public function edit(string $id): View
    {
        $category = Category::findOrFail($id);
        $this->requireUnlocked($category);

        $this->lock($category);

        return view('admin.taxonomy.categories.edit', compact('category'));
    }

    public function update(CategoryRequest $request, Category $category): RedirectResponse
    {
        $this->requireUnlocked($category);

        $data = $request->validated();

        $category->update($data);

        $this->unlock($category);

        return redirect()
            ->route('admin.categories.show', $category)
            ->with('success', __('app.taxonomy.category.updated'));
    }

    public function destroy(Category $category): RedirectResponse
    {
        $this->requireUnlocked($category);

        $this->unlock($category);

        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', __('app.taxonomy.category.deleted'));
    }
}
