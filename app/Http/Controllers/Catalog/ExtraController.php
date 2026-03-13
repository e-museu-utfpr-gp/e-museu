<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Requests\Catalog\ItemContributionValidator;
use App\Http\Requests\Catalog\SingleExtraRequest;
use App\Services\Catalog\ExtraService;
use App\Services\Collaborator\CollaboratorService;
use Illuminate\Http\RedirectResponse;

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

        return $extraService->storeSingleExtra(
            $collaboratorService,
            $validatedData['collaborator'],
            $validatedData['extra']
        );
    }
}
