<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Services\Catalog\ExtraService;
use App\Services\Collaborator\CollaboratorService;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Catalog\{ItemContributionValidator, SingleExtraRequest};

class ExtraController extends Controller
{
    /**
     * Store a single extra from public contribution (e.g. modal on item show page).
     */
    public function store(
        SingleExtraRequest $request,
        ItemContributionValidator $itemContributionValidator,
        ExtraService $extraService,
        CollaboratorService $collaboratorService
    ): RedirectResponse {
        $validatedData = $itemContributionValidator->validateSingleExtra($request);

        $result = $extraService->storeSingleExtra(
            $collaboratorService,
            $validatedData['collaborator'],
            $validatedData['extra']
        );

        if ($result['status'] === 'internal_blocked') {
            return back()->withErrors(['contact' => __('app.collaborator.contact_reserved_for_internal')]);
        }

        if ($result['status'] === 'collaborator_blocked') {
            return back()->withErrors(['blocked' => __('app.collaborator.blocked_from_registering')]);
        }

        return back()->with('success', __('app.catalog.extra.contribution_success'));
    }
}
