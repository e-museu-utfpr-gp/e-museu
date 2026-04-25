<?php

declare(strict_types=1);

namespace App\Http\Controllers\Catalog;

use App\Actions\Catalog\ResolveSingleExtraCollaboratorAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Catalog\{ItemContributionValidator, SingleExtraRequest};
use App\Models\Language;
use App\Services\Catalog\{CatalogContributionCompletionService, ExtraService};
use App\Services\Collaborator\CollaboratorService;
use App\Support\Catalog\PublicCatalogContributionOutcome;
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
        ResolveSingleExtraCollaboratorAction $resolveSingleExtraCollaborator,
        CatalogContributionCompletionService $catalogContributionCompletion,
    ): RedirectResponse {
        $validatedData = $itemContributionValidator->validateSingleExtra($request);

        $contentLocaleCode = (string) ($validatedData['extra']['content_locale'] ?? '');
        $contentLanguageId = Language::idForCode($contentLocaleCode);

        $result = $extraService->storeSingleExtra(
            $collaboratorService,
            $resolveSingleExtraCollaborator,
            $validatedData['collaborator'],
            $validatedData['extra']
        );

        PublicCatalogContributionOutcome::throwUnlessOk($result);

        $catalogContributionCompletion->afterExtra($result['extra'] ?? null, $contentLanguageId);

        return back()->with('success', __('app.catalog.extra.contribution_success'));
    }
}
