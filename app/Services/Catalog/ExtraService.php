<?php

namespace App\Services\Catalog;

use App\Enums\Collaborator\CollaboratorRole;
use App\Models\Catalog\Extra;
use App\Models\Catalog\Item;
use App\Models\Collaborator\Collaborator;
use App\Services\Collaborator\CollaboratorService;
use Illuminate\Http\RedirectResponse;

class ExtraService
{
    /**
     * Create extra records for an item (contribution flow).
     *
     * @param  array<int, array<string, mixed>>  $extrasData
     */
    public function createForItem(Item $item, Collaborator $collaborator, array $extrasData): void
    {
        foreach ($extrasData as $extraItemData) {
            $extraItemData['collaborator_id'] = $collaborator->id;
            $extraItemData['item_id'] = $item->id;
            Extra::create($extraItemData);
        }
    }

    /**
     * Create a single extra (e.g. from storeSingleExtra contribution).
     *
     * @param  array<string, mixed>  $extraData
     */
    public function create(array $extraData, Collaborator $collaborator): Extra
    {
        $extraData['collaborator_id'] = $collaborator->id;

        return Extra::create($extraData);
    }

    /**
     * Store a single extra from contribution (resolve collaborator, validate, create extra).
     *
     * @param  array<string, mixed>  $collaboratorData
     * @param  array<string, mixed>  $extraData
     */
    public function storeSingleExtra(
        CollaboratorService $collaboratorService,
        array $collaboratorData,
        array $extraData
    ): RedirectResponse {
        $collaborator = $collaboratorService->resolveOrCreateCollaborator($collaboratorData);

        if ($collaborator->role === CollaboratorRole::INTERNAL) {
            return back()->withErrors(['contact' => __('app.collaborator.contact_reserved_for_internal')]);
        }

        if ($collaborator->blocked === true) {
            return back()->withErrors(['blocked' => __('app.collaborator.blocked_from_registering')]);
        }

        $this->create($extraData, $collaborator);

        return back()->with('success', __('app.catalog.extra.contribution_success'));
    }
}
