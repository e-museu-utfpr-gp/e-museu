<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Taxonomy;

use App\Http\Controllers\Admin\AdminBaseController;
use App\Http\Requests\Admin\Taxonomy\AdminTagRequest;
use App\Models\Language;
use App\Models\Taxonomy\Tag;
use App\Services\Identity\LockService;
use Illuminate\View\View;
use App\Services\Taxonomy\{TagCategoryService, TagService};
use App\Support\Admin\Ai\{AdminAiViewData, AdminContentTranslationRegistry};
use App\Support\Admin\{AdminEditHeadingLocale, AdminIndexTableView};
use Illuminate\Http\{RedirectResponse, Request};

class AdminTagController extends AdminBaseController
{
    public function index(Request $request, TagService $tagService): View
    {
        $result = $tagService->getPaginatedTagsForAdminIndex($request);

        return view('pages.admin.taxonomy.tags.index', array_merge([
            'tags' => $result['tags'],
            'count' => $result['count'],
        ], AdminIndexTableView::taxonomyTags()));
    }

    public function show(Tag $tag): View
    {
        return view('pages.admin.taxonomy.tags.show', compact('tag'));
    }

    public function create(TagCategoryService $tagCategoryService): View
    {
        $categories = $tagCategoryService->getForForm();

        $aiTranslationViewData = AdminAiViewData::forTranslationResource(
            AdminContentTranslationRegistry::RESOURCE_TAG
        );

        return view('pages.admin.taxonomy.tags.create', array_merge([
            'categories' => $categories,
            'contentLanguages' => Language::forCatalogContentForms(),
            'preferredContentTabLanguageId' => AdminEditHeadingLocale::preferredContentTabLanguageId(),
        ], $aiTranslationViewData));
    }

    public function store(AdminTagRequest $request, TagService $tagService): RedirectResponse
    {
        $data = $request->validated();
        $tag = $tagService->createFromAdminRequestData($data);

        return redirect()->route('admin.taxonomy.tags.show', $tag)->with('success', __('app.taxonomy.tag.created'));
    }

    public function edit(
        Tag $tag,
        TagCategoryService $tagCategoryService,
        LockService $lockService,
        AdminEditHeadingLocale $headingLocale
    ): View {
        $lockService->requireUnlockedThenLock($tag);

        $categories = $tagCategoryService->getForForm();

        $aiTranslationViewData = AdminAiViewData::forTranslationResource(
            AdminContentTranslationRegistry::RESOURCE_TAG
        );

        return view('pages.admin.taxonomy.tags.edit', array_merge([
            'tag' => $tag,
            'categories' => $categories,
            'contentLanguages' => Language::forCatalogContentForms(),
        ], $headingLocale->resolveFor($tag), $aiTranslationViewData));
    }

    public function update(
        AdminTagRequest $request,
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
