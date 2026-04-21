<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Collaborator;

use App\Http\Controllers\Admin\AdminBaseController;
use App\Models\Collaborator\Collaborator;
use App\Services\Collaborator\CollaboratorService;
use App\Services\Identity\LockService;
use App\Support\Admin\AdminIndexTableView;
use Illuminate\View\View;
use App\Http\Requests\Admin\Collaborator\{AdminStoreCollaboratorRequest, AdminUpdateCollaboratorRequest};
use Illuminate\Http\{RedirectResponse, Request};

class AdminCollaboratorController extends AdminBaseController
{
    public function index(Request $request, CollaboratorService $collaboratorService): View
    {
        $result = $collaboratorService->getPaginatedCollaboratorsForAdminIndex($request);

        return view('pages.admin.collaborators.index', array_merge([
            'collaborators' => $result['collaborators'],
            'count' => $result['count'],
        ], AdminIndexTableView::collaborators()));
    }

    public function show(Collaborator $collaborator): View
    {
        return view('pages.admin.collaborators.show', compact('collaborator'));
    }

    public function create(): View
    {
        return view('pages.admin.collaborators.create');
    }

    public function store(
        AdminStoreCollaboratorRequest $request,
        CollaboratorService $collaboratorService
    ): RedirectResponse {
        $data = array_merge(
            $request->validated(),
            ['blocked' => $request->boolean('blocked', false)]
        );
        $collaborator = $collaboratorService->createCollaborator($data);

        return redirect()
            ->route('admin.collaborators.show', $collaborator)
            ->with('success', __('app.collaborator.created'));
    }

    public function edit(Collaborator $collaborator, LockService $lockService): View
    {
        $lockService->requireUnlockedThenLock($collaborator);

        return view('pages.admin.collaborators.edit', compact('collaborator'));
    }

    public function update(
        AdminUpdateCollaboratorRequest $request,
        Collaborator $collaborator,
        CollaboratorService $collaboratorService,
        LockService $lockService
    ): RedirectResponse {
        $lockService->requireUnlocked($collaborator);

        $collaboratorService->updateCollaborator($collaborator, $request->validated());

        $lockService->unlock($collaborator);

        return redirect()
            ->route('admin.collaborators.show', $collaborator)
            ->with('success', __('app.collaborator.updated'));
    }

    public function destroy(
        Collaborator $collaborator,
        CollaboratorService $collaboratorService,
        LockService $lockService
    ): RedirectResponse {
        $lockService->requireUnlocked($collaborator);

        $lockService->unlock($collaborator);
        $collaboratorService->deleteCollaborator($collaborator);

        return redirect()
            ->route('admin.collaborators.index')
            ->with('success', __('app.collaborator.deleted'));
    }
}
