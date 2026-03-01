<?php

namespace App\Http\Controllers\Admin\Taxonomy;

use App\Http\Controllers\Admin\AdminBaseController;
use App\Http\Controllers\Admin\Concerns\LocksSubject;
use App\Http\Requests\Taxonomy\TagCategoryRequest;
use App\Models\Taxonomy\TagCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminTagCategoryController extends AdminBaseController
{
    use LocksSubject;

    public function index(Request $request): View
    {
        $query = TagCategory::query();
        $count = TagCategory::count();

        if ($request->search_column && $request->search) {
            $query->where($request->search_column, 'LIKE', "%{$request->search}%");
        }

        if ($request->sort && $request->order) {
            $query->orderBy($request->sort, $request->order);
        }

        $tagCategories = $query->paginate(30)->withQueryString();

        return view('admin.taxonomy.tag-categories.index', compact('tagCategories', 'count'));
    }

    public function create(): View
    {
        return view('admin.taxonomy.tag-categories.create');
    }

    public function store(TagCategoryRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $tagCategory = TagCategory::create($data);

        return redirect()
            ->route('admin.tag-categories.show', $tagCategory)
            ->with('success', __('app.taxonomy.tag_category.created'));
    }

    public function show(string $id): View
    {
        $tagCategory = TagCategory::findOrFail($id);

        return view('admin.taxonomy.tag-categories.show', compact('tagCategory'));
    }

    public function edit(string $id): View
    {
        $tagCategory = TagCategory::findOrFail($id);
        $this->requireUnlocked($tagCategory);

        $this->lock($tagCategory);

        return view('admin.taxonomy.tag-categories.edit', compact('tagCategory'));
    }

    public function update(TagCategoryRequest $request, TagCategory $tagCategory): RedirectResponse
    {
        $this->requireUnlocked($tagCategory);

        $data = $request->validated();

        $tagCategory->update($data);

        $this->unlock($tagCategory);

        return redirect()
            ->route('admin.tag-categories.show', $tagCategory)
            ->with('success', __('app.taxonomy.tag_category.updated'));
    }

    public function destroy(TagCategory $tagCategory): RedirectResponse
    {
        $this->requireUnlocked($tagCategory);

        $this->unlock($tagCategory);

        $tagCategory->delete();

        return redirect()
            ->route('admin.tag-categories.index')
            ->with('success', __('app.taxonomy.tag_category.deleted'));
    }
}
