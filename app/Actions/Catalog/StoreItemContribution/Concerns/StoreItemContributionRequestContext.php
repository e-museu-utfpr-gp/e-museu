<?php

declare(strict_types=1);

namespace App\Actions\Catalog\StoreItemContribution\Concerns;

use App\Http\Requests\Catalog\ItemContributionValidator;
use App\Models\Catalog\Item;
use App\Models\Language;
use App\Services\Catalog\CatalogContributionCompletionService;
use Illuminate\Http\Request;

/**
 * Request validation and post-success side effects for the public store-item contribution flow.
 *
 * @see \App\Actions\Catalog\StoreItemContribution\StoreItemContributionAction
 */
final class StoreItemContributionRequestContext
{
    public function __construct(
        private readonly ItemContributionValidator $validator,
        private readonly CatalogContributionCompletionService $catalogContributionCompletion,
    ) {
    }

    /**
     * @return array{
     *     collaborator: array<string, mixed>,
     *     item: array<string, mixed>,
     *     tags: array<int, array<string, mixed>>,
     *     extras: array<int, array<string, mixed>>,
     *     components: array<int, array<string, mixed>>,
     * }
     */
    public function validateStore(Request $request): array
    {
        return $this->validator->validateStore($request);
    }

    public function languageIdForValidatedLocaleCode(string $localeCode): int
    {
        return Language::idForCode($localeCode);
    }

    public function runPostContributionCompletion(?Item $item, int $contentLanguageId): void
    {
        $this->catalogContributionCompletion->afterItem($item, $contentLanguageId);
    }
}
