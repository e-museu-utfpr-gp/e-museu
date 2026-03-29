<?php

namespace App\Services\Catalog;

use App\Enums\Collaborator\CollaboratorRole;
use App\Models\Catalog\Extra;
use App\Models\Catalog\Item;
use App\Models\Collaborator\Collaborator;
use App\Services\Collaborator\CollaboratorService;
use App\Support\Admin\AdminIndexConfig;
use App\Support\Content\TranslatablePayload;
use App\Support\Admin\AdminIndexQueryBuilder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
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
        $split = TranslatablePayload::split($data, TranslatablePayload::EXTRA_KEYS);
        $extra = Extra::create($split['persist']);
        $extra->syncPrimaryLocaleTranslation([
            'info' => (string) ($split['translation']['info'] ?? ''),
        ]);

        return $extra;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updateExtra(Extra $extra, array $data): void
    {
        $split = TranslatablePayload::split($data, TranslatablePayload::EXTRA_KEYS);
        if ($split['persist'] !== []) {
            $extra->update($split['persist']);
        }
        if (array_key_exists('info', $split['translation'])) {
            $extra->syncPrimaryLocaleTranslation([
                'info' => (string) $split['translation']['info'],
            ]);
        }
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
            $split = TranslatablePayload::split($extraItemData, TranslatablePayload::EXTRA_KEYS);
            $split['persist']['collaborator_id'] = $collaborator->id;
            $split['persist']['item_id'] = $item->id;
            $extra = Extra::create($split['persist']);
            $extra->syncPrimaryLocaleTranslation([
                'info' => (string) ($split['translation']['info'] ?? ''),
            ]);
        }
    }

    /**
     * @param  array<string, mixed>  $extraData
     */
    public function create(array $extraData, Collaborator $collaborator): Extra
    {
        $split = TranslatablePayload::split($extraData, TranslatablePayload::EXTRA_KEYS);
        $split['persist']['collaborator_id'] = $collaborator->id;
        $extra = Extra::create($split['persist']);
        $extra->syncPrimaryLocaleTranslation([
            'info' => (string) ($split['translation']['info'] ?? ''),
        ]);

        return $extra;
    }

    /**
     * Handle single extra creation from public contribution.
     *
     * @param  array<string, mixed>  $collaboratorData Collaborator data validated by the request.
     * @param  array<string, mixed>  $extraData        Extra data validated by the request.
     * @return array{status: 'ok'|'internal_blocked'|'collaborator_blocked'}
     */
    public function storeSingleExtra(
        CollaboratorService $collaboratorService,
        array $collaboratorData,
        array $extraData
    ): array {
        $collaborator = $collaboratorService->resolveOrCreateCollaborator($collaboratorData);

        if ($collaborator->role === CollaboratorRole::INTERNAL) {
            return ['status' => 'internal_blocked'];
        }

        if ($collaborator->blocked === true) {
            return ['status' => 'collaborator_blocked'];
        }

        $this->create($extraData, $collaborator);

        return ['status' => 'ok'];
    }
}
