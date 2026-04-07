<?php

namespace App\Services\Catalog;

use App\Enums\Content\ContentLanguage;
use App\Models\Language;
use App\Support\Content\ContentLocaleFallback;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

/**
 * Content locale choices for public catalog contribution and extra forms (not item persistence).
 */
final class ContributionContentLocaleService
{
    /**
     * @return array{
     *     contributionLanguages: EloquentCollection<int, Language>,
     *     defaultContentLocale: string
     * }
     */
    public function formOptions(): array
    {
        $contributionLanguages = Language::forCatalogContentForms();
        $defaultContentLocale = ContentLocaleFallback::normalizedAppLocaleCode();
        if (Language::tryIdForCode($defaultContentLocale) === null) {
            $defaultContentLocale = ContentLanguage::defaultForForms()->value;
        }

        return [
            'contributionLanguages' => $contributionLanguages,
            'defaultContentLocale' => $defaultContentLocale,
        ];
    }

    /**
     * After request validation ensures {@code $code} exists in {@see Language}.
     */
    public function languageIdForValidatedCode(string $code): int
    {
        return Language::idForCode($code);
    }
}
