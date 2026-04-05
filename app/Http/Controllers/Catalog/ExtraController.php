<?php

namespace App\Http\Controllers\Catalog;

use App\Actions\Catalog\CompleteCatalogExtraContributionAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Catalog\{ItemContributionValidator, SingleExtraRequest};
use App\Models\Language;
use App\Services\Catalog\ExtraService;
use App\Support\Catalog\PublicCatalogContributionOutcome;
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
        CollaboratorService $collaboratorService,
        CompleteCatalogExtraContributionAction $completeCatalogExtraContribution,
    ): RedirectResponse {
        $validatedData = $itemContributionValidator->validateSingleExtra($request);

        $contentLocaleCode = (string) ($validatedData['extra']['content_locale'] ?? '');
        $contentLanguageId = Language::idForCode($contentLocaleCode);

        $result = $extraService->storeSingleExtra(
            $collaboratorService,
            $validatedData['collaborator'],
            $validatedData['extra']
        );

        PublicCatalogContributionOutcome::throwUnlessOk($result);

        $completeCatalogExtraContribution->handle($result['extra'] ?? null, $contentLanguageId);

        return back()->with('success', __('app.catalog.extra.contribution_success'));
    }
}
