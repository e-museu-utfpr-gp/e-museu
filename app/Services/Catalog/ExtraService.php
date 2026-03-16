<?php

namespace App\Services\Catalog;

use App\Enums\Collaborator\CollaboratorRole;
use App\Models\Catalog\Extra;
use App\Models\Catalog\Item;
use App\Models\Collaborator\Collaborator;
use App\Services\Collaborator\CollaboratorService;
use App\Support\AdminIndexQueryBuilder;
use App\Support\AdminIndexConfig;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ExtraService
{
    /**
     * @return array{extras: LengthAwarePaginator<int, Extra>, count: int}
     */
    public function getPaginatedExtrasForAdminIndex(Request $request): array
    {
        $count = Extra::count();
        $query = Extra::query()->forAdminList();

        AdminIndexQueryBuilder::build($query, $request, AdminIndexConfig::extras());

        $extras = $query->paginate(30)->withQueryString();

        return ['extras' => $extras, 'count' => $count];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function createExtra(array $data): Extra
    {
        return Extra::create($data);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updateExtra(Extra $extra, array $data): void
    {
        $extra->update($data);
    }

    public function deleteExtra(Extra $extra): void
    {
        $extra->delete();
    }

    /**
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
     * @param  array<string, mixed>  $extraData
     */
    public function create(array $extraData, Collaborator $collaborator): Extra
    {
        $extraData['collaborator_id'] = $collaborator->id;

        return Extra::create($extraData);
    }

    /**
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
