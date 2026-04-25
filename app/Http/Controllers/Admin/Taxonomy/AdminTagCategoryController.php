<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Taxonomy;

use App\Http\Controllers\Admin\AdminBaseController;
use App\Http\Requests\Admin\Taxonomy\AdminTagCategoryRequest;
use App\Models\Language;
use App\Models\Taxonomy\TagCategory;
use App\Services\Identity\LockService;
use App\Services\Taxonomy\TagCategoryService;
use Illuminate\View\View;
use App\Support\Admin\Ai\{AdminAiViewData, AdminContentTranslationRegistry};
use App\Support\Admin\{AdminEditHeadingLocale, AdminIndexTableView};
use Illuminate\Http\{RedirectResponse, Request};

class AdminTagCategoryController extends AdminBaseController
{
    public function index(Request $request, TagCategoryService $tagCategoryService): View
    {
        $result = $tagCategoryService->getPaginatedTagCategoriesForAdminIndex($request);

        return view('pages.admin.taxonomy.tag-categories.index', array_merge([
            'tagCategories' => $result['tagCategories'],
            'count' => $result['count'],
        ], AdminIndexTableView::taxonomyTagCategories()));
    }

    public function create(): View
    {
        $aiTranslationViewData = AdminAiViewData::forTranslationResource(
            AdminContentTranslationRegistry::RESOURCE_TAG_CATEGORY
        );

        return view('pages.admin.taxonomy.tag-categories.create', array_merge([
            'contentLanguages' => Language::forCatalogContentForms(),
            'preferredContentTabLanguageId' => AdminEditHeadingLocale::preferredContentTabLanguageId(),
        ], $aiTranslationViewData));
    }

    public function store(AdminTagCategoryRequest $request, TagCategoryService $tagCategoryService): RedirectResponse
    {
        $tagCategory = $tagCategoryService->createTagCategory($request->validated());

        return redirect()
            ->route('admin.taxonomy.tag-categories.show', $tagCategory)
            ->with('success', __('app.taxonomy.tag_category.created'));
    }

    public function show(TagCategory $tagCategory): View
    {
        return view('pages.admin.taxonomy.tag-categories.show', compact('tagCategory'));
    }

    public function edit(
        TagCategory $tagCategory,
        LockService $lockService,
        AdminEditHeadingLocale $headingLocale
    ): View {
        $lockService->requireUnlockedThenLock($tagCategory);

        $aiTranslationViewData = AdminAiViewData::forTranslationResource(
            AdminContentTranslationRegistry::RESOURCE_TAG_CATEGORY
        );

        return view('pages.admin.taxonomy.tag-categories.edit', array_merge([
            'tagCategory' => $tagCategory,
            'contentLanguages' => Language::forCatalogContentForms(),
        ], $headingLocale->resolveFor($tagCategory), $aiTranslationViewData));
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
            ->route('admin.taxonomy.tag-categories.show', $tagCategory)
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
            ->route('admin.taxonomy.tag-categories.index')
            ->with('success', __('app.taxonomy.tag_category.deleted'));
    }
}
