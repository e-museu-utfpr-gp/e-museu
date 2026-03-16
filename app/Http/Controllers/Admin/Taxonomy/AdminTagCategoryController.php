<?php

namespace App\Http\Controllers\Admin\Taxonomy;

use App\Http\Controllers\Admin\AdminBaseController;
use App\Http\Requests\Admin\Taxonomy\AdminTagCategoryRequest;
use App\Models\Taxonomy\TagCategory;
use App\Services\Identity\LockService;
use App\Services\Taxonomy\TagCategoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminTagCategoryController extends AdminBaseController
{
    public function index(Request $request, TagCategoryService $tagCategoryService): View
    {
        $result = $tagCategoryService->getPaginatedTagCategoriesForAdminIndex($request);

        return view('admin.taxonomy.tag-categories.index', [
            'tagCategories' => $result['tagCategories'],
            'count' => $result['count'],
        ]);
    }

    public function create(): View
    {
        return view('admin.taxonomy.tag-categories.create');
    }

    public function store(AdminTagCategoryRequest $request, TagCategoryService $tagCategoryService): RedirectResponse
    {
        $tagCategory = $tagCategoryService->createTagCategory($request->validated());

        return redirect()
            ->route('admin.tag-categories.show', $tagCategory)
            ->with('success', __('app.taxonomy.tag_category.created'));
    }

    public function show(TagCategory $tagCategory): View
    {
        return view('admin.taxonomy.tag-categories.show', compact('tagCategory'));
    }

    public function edit(TagCategory $tagCategory, LockService $lockService): View
    {
        $lockService->requireUnlocked($tagCategory);

        $lockService->lock($tagCategory);

        return view('admin.taxonomy.tag-categories.edit', compact('tagCategory'));
    }

    public function update(
        AdminTagCategoryRequest $request,
        TagCategory $tagCategory,
        TagCategoryService $tagCategoryService,
        LockService $lockService
    ): RedirectResponse {
        $lockService->requireUnlocked($tagCategory);

        $data = $request->validated();

        $tagCategoryService->updateTagCategory($tagCategory, $data);

        $lockService->unlock($tagCategory);

        return redirect()
            ->route('admin.tag-categories.show', $tagCategory)
            ->with('success', __('app.taxonomy.tag_category.updated'));
    }

    public function destroy(
        TagCategory $tagCategory,
        TagCategoryService $tagCategoryService,
        LockService $lockService
    ): RedirectResponse {
        $lockService->requireUnlocked($tagCategory);

        $lockService->unlock($tagCategory);

        $tagCategoryService->deleteTagCategory($tagCategory);

        return redirect()
            ->route('admin.tag-categories.index')
            ->with('success', __('app.taxonomy.tag_category.deleted'));
    }
}
