<?php

namespace App\Services\Catalog;

use App\Enums\Collaborator\CollaboratorRole;
use App\Models\Language;
use App\Models\Collaborator\Collaborator;
use App\Services\Collaborator\CollaboratorService;
use App\Support\Content\TranslatablePayload;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use App\Models\Catalog\{Extra, Item};
use App\Support\Admin\{AdminIndexConfig, AdminIndexQueryBuilder};

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
        $translations = $data['translations'] ?? [];
        $extra = Extra::create(Arr::except($data, ['translations']));
        $extra->syncTranslationsFromAdminForm($translations);

        return $extra;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updateExtra(Extra $extra, array $data): void
    {
        $translations = $data['translations'] ?? [];
        $persist = Arr::except($data, ['translations']);
        if ($persist !== []) {
            $extra->update($persist);
        }
        if ($translations !== []) {
            $extra->syncTranslationsFromAdminForm($translations);
        }
    }

    public function deleteExtra(Extra $extra): void
    {
        $extra->delete();
    }

    /**
     * @param  array<int, array<string, mixed>>  $extrasData
     */
    public function createForItem(
        Item $item,
        Collaborator $collaborator,
        array $extrasData,
        ?int $contentLanguageId = null
    ): void {
        $langId = $contentLanguageId ?? Language::idForPreferredFormLocale();

        foreach ($extrasData as $extraItemData) {
            $split = TranslatablePayload::split($extraItemData, TranslatablePayload::EXTRA_KEYS);
            $split['persist']['collaborator_id'] = $collaborator->id;
            $split['persist']['item_id'] = $item->id;
            $extra = Extra::create($split['persist']);
            $extra->syncTranslationForLanguage($langId, [
                'info' => (string) ($split['translation']['info'] ?? ''),
            ]);
        }
    }

    /**
     * @param  array<string, mixed>  $extraData
     */
    public function create(array $extraData, Collaborator $collaborator, ?int $contentLanguageId = null): Extra
    {
        $langId = $contentLanguageId ?? Language::idForPreferredFormLocale();
        $split = TranslatablePayload::split($extraData, TranslatablePayload::EXTRA_KEYS);
        $split['persist']['collaborator_id'] = $collaborator->id;
        $extra = Extra::create($split['persist']);
        $extra->syncTranslationForLanguage($langId, [
            'info' => (string) ($split['translation']['info'] ?? ''),
        ]);

        return $extra;
    }

    /**
     * Handle single extra creation from public contribution.
     *
     * @param  array<string, mixed>  $collaboratorData Collaborator data validated by the request.
     * @param  array<string, mixed>  $extraData        Extra data validated by the request.
     * @return array{
     *     status: 'ok'|'collaborator_invalid'|'internal_blocked'|'collaborator_blocked'|'email_unverified',
     *     extra?: Extra,
     * }
     */
    public function storeSingleExtra(
        CollaboratorService $collaboratorService,
        array $collaboratorData,
        array $extraData
    ): array {
        return DB::transaction(function () use ($collaboratorService, $collaboratorData, $extraData): array {
            $collaboratorId = (int) ($extraData['collaborator_id'] ?? 0);
            unset($extraData['collaborator_id']);

            $collaborator = Collaborator::query()->find($collaboratorId);
            if ($collaborator === null) {
                return ['status' => 'collaborator_invalid'];
            }

            if ($collaborator->role === CollaboratorRole::INTERNAL) {
                return ['status' => 'internal_blocked'];
            }

            if ($collaborator->blocked === true) {
                return ['status' => 'collaborator_blocked'];
            }

            $gate = $collaboratorService->publicContributionCollaboratorGate($collaborator, $collaboratorData);
            if ($gate === 'email_unverified') {
                return ['status' => 'email_unverified'];
            }

            $collaboratorService->applySubmittedFullNameAfterVerifiedContribution(
                $collaborator,
                (string) ($collaboratorData['full_name'] ?? ''),
            );
            $collaborator->refresh();

            $localeCode = (string) ($extraData['content_locale'] ?? '');
            unset($extraData['content_locale']);
            $langId = Language::idForCode($localeCode);

            $extra = $this->create($extraData, $collaborator, $langId);

            return ['status' => 'ok', 'extra' => $extra];
        });
    }
}
