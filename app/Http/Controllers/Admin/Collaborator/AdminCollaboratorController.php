<?php

namespace App\Http\Controllers\Admin\Collaborator;

use App\Http\Controllers\Admin\AdminBaseController;
use App\Http\Requests\Admin\Collaborator\AdminStoreCollaboratorRequest;
use App\Http\Requests\Admin\Collaborator\AdminUpdateCollaboratorRequest;
use App\Models\Collaborator\Collaborator;
use App\Services\Collaborator\CollaboratorService;
use App\Services\Identity\LockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminCollaboratorController extends AdminBaseController
{
    public function index(Request $request, CollaboratorService $collaboratorService): View
    {
        $result = $collaboratorService->getPaginatedCollaboratorsForAdminIndex($request);

        return view('admin.collaborators.index', [
            'collaborators' => $result['collaborators'],
            'count' => $result['count'],
        ]);
    }

    public function show(Collaborator $collaborator): View
    {
        return view('admin.collaborators.show', compact('collaborator'));
    }

    public function create(): View
    {
        return view('admin.collaborators.create');
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
        $lockService->requireUnlocked($collaborator);
        $lockService->lock($collaborator);

        return view('admin.collaborators.edit', compact('collaborator'));
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
