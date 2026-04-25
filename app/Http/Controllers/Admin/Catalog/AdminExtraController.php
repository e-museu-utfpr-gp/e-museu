<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Catalog;

use App\Http\Controllers\Admin\AdminBaseController;
use App\Http\Requests\Admin\Catalog\AdminStoreExtraRequest;
use App\Models\Language;
use App\Models\Catalog\Extra;
use App\Services\Collaborator\CollaboratorService;
use App\Services\Identity\LockService;
use Illuminate\View\View;
use App\Services\Catalog\ExtraService;
use App\Services\Catalog\ItemCategoryService;
use App\Support\Admin\Ai\{AdminAiViewData, AdminContentTranslationRegistry};
use App\Support\Admin\{AdminEditHeadingLocale, AdminIndexTableView};
use Illuminate\Http\{RedirectResponse, Request};

class AdminExtraController extends AdminBaseController
{
    public function index(Request $request, ExtraService $extraService): View
    {
        $result = $extraService->getPaginatedExtrasForAdminIndex($request);

        return view('pages.admin.catalog.extras.index', array_merge([
            'extras' => $result['extras'],
            'count' => $result['count'],
        ], AdminIndexTableView::catalogExtras()));
    }

    public function show(Extra $extra): View
    {
        $extra->load([
            'collaborator',
            'item.translations.language',
            'item.images',
            'item.coverImage',
            'item.itemCategory.translations.language',
            'item.collaborator',
        ]);

        return view('pages.admin.catalog.extras.show', compact('extra'));
    }

    public function create(
        ItemCategoryService $itemCategoryService,
        CollaboratorService $collaboratorService
    ): View {
        $aiTranslationViewData = AdminAiViewData::forTranslationResource(
            AdminContentTranslationRegistry::RESOURCE_EXTRA
        );

        return view('pages.admin.catalog.extras.create', array_merge([
            'itemCategories' => $itemCategoryService->getForForm(),
            'collaborators' => $collaboratorService->getForForm(),
            'contentLanguages' => Language::forCatalogContentForms(),
            'preferredContentTabLanguageId' => AdminEditHeadingLocale::preferredContentTabLanguageId(),
        ], $aiTranslationViewData));
    }

    public function store(AdminStoreExtraRequest $request, ExtraService $extraService): RedirectResponse
    {
        $extra = $extraService->createExtra($request->validated());

        return redirect()->route('admin.catalog.extras.show', $extra)->with('success', __('app.catalog.extra.created'));
    }

    public function edit(
        Extra $extra,
        ItemCategoryService $itemCategoryService,
        CollaboratorService $collaboratorService,
        LockService $lockService,
        AdminEditHeadingLocale $headingLocale
    ): View {
        $lockService->requireUnlockedThenLock($extra);

        $extra->load(['translations.language', 'item.itemCategory', 'collaborator']);

        $aiTranslationViewData = AdminAiViewData::forTranslationResource(
            AdminContentTranslationRegistry::RESOURCE_EXTRA
        );

        return view('pages.admin.catalog.extras.edit', array_merge([
            'extra' => $extra,
            'itemCategories' => $itemCategoryService->getForForm(),
            'collaborators' => $collaboratorService->getForForm(),
            'contentLanguages' => Language::forCatalogContentForms(),
        ], $headingLocale->resolveFor($extra), $aiTranslationViewData));
    }

    public function update(
        AdminStoreExtraRequest $request,
        Extra $extra,
        ExtraService $extraService,
        LockService $lockService
    ): RedirectResponse {
        $lockService->requireUnlocked($extra);

        $extraService->updateExtra($extra, $request->validated());

        $lockService->unlock($extra);

        return redirect()->route('admin.catalog.extras.show', $extra)->with('success', __('app.catalog.extra.updated'));
    }

    public function destroy(Extra $extra, ExtraService $extraService, LockService $lockService): RedirectResponse
    {
        $lockService->requireUnlocked($extra);

        $lockService->unlock($extra);
        $extraService->deleteExtra($extra);

        return redirect()->route('admin.catalog.extras.index')->with('success', __('app.catalog.extra.deleted'));
    }
}
