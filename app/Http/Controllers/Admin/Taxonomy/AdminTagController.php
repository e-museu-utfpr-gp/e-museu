<?php

namespace App\Http\Controllers\Admin\Taxonomy;

use App\Http\Controllers\Admin\AdminBaseController;
use App\Http\Requests\Admin\Taxonomy\AdminSingleTagRequest;
use App\Services\Taxonomy\TagService;
use App\Services\Identity\LockService;
use App\Services\Taxonomy\TagCategoryService;
use App\Models\Taxonomy\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminTagController extends AdminBaseController
{
    public function index(Request $request, TagService $tagService): View
    {
        $result = $tagService->getPaginatedTagsForAdminIndex($request);

        return view('pages.admin.taxonomy.tags.index', [
            'tags' => $result['tags'],
            'count' => $result['count'],
        ]);
    }

    public function show(Tag $tag): View
    {
        return view('pages.admin.taxonomy.tags.show', compact('tag'));
    }

    public function create(TagCategoryService $tagCategoryService): View
    {
        $categories = $tagCategoryService->getForForm();

        return view('pages.admin.taxonomy.tags.create', compact('categories'));
    }

    public function store(AdminSingleTagRequest $request, TagService $tagService): RedirectResponse
    {
        $data = $request->validated();
        $tag = $tagService->createFromAdminRequestData($data);

        return redirect()->route('admin.taxonomy.tags.show', $tag)->with('success', __('app.taxonomy.tag.created'));
    }

    public function edit(Tag $tag, TagCategoryService $tagCategoryService, LockService $lockService): View
    {
        $lockService->requireUnlocked($tag);

        $categories = $tagCategoryService->getForForm();

        $lockService->lock($tag);

        return view('pages.admin.taxonomy.tags.edit', compact('tag', 'categories'));
    }

    public function update(
        AdminSingleTagRequest $request,
        Tag $tag,
        TagService $tagService,
        LockService $lockService
    ): RedirectResponse {
        $lockService->requireUnlocked($tag);

        $tagService->updateFromAdminRequestData($tag, $request->validated());

        $lockService->unlock($tag);

        return redirect()->route('admin.taxonomy.tags.show', $tag)->with('success', __('app.taxonomy.tag.updated'));
    }

    public function destroy(Tag $tag, TagService $tagService, LockService $lockService): RedirectResponse
    {
        $lockService->requireUnlocked($tag);

        $lockService->unlock($tag);

        $tagService->deleteTag($tag);

        return redirect()->route('admin.taxonomy.tags.index')->with('success', __('app.taxonomy.tag.deleted'));
    }
}
