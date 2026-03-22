<?php

namespace App\Http\Controllers\Admin\Catalog;

use App\Http\Controllers\Admin\AdminBaseController;
use App\Http\Requests\Catalog\SingleExtraRequest;
use App\Models\Catalog\Extra;
use App\Services\Catalog\ExtraService;
use App\Services\Catalog\ItemCategoryService;
use App\Services\Collaborator\CollaboratorService;
use App\Services\Identity\LockService;
use App\Support\Admin\AdminIndexTableView;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

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
        return view('pages.admin.catalog.extras.show', compact('extra'));
    }

    public function create(
        ItemCategoryService $itemCategoryService,
        CollaboratorService $collaboratorService
    ): View {
        return view('pages.admin.catalog.extras.create', [
            'itemCategories' => $itemCategoryService->getForForm(),
            'collaborators' => $collaboratorService->getForForm(),
        ]);
    }

    public function store(SingleExtraRequest $request, ExtraService $extraService): RedirectResponse
    {
        $extra = $extraService->createExtra($request->validated());

        return redirect()->route('admin.catalog.extras.show', $extra)->with('success', __('app.catalog.extra.created'));
    }

    public function edit(
        Extra $extra,
        ItemCategoryService $itemCategoryService,
        CollaboratorService $collaboratorService,
        LockService $lockService
    ): View {
        $lockService->requireUnlocked($extra);
        $lockService->lock($extra);

        return view('pages.admin.catalog.extras.edit', [
            'extra' => $extra,
            'itemCategories' => $itemCategoryService->getForForm(),
            'collaborators' => $collaboratorService->getForForm(),
        ]);
    }

    public function update(
        SingleExtraRequest $request,
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
