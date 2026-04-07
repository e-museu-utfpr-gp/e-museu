<?php

namespace App\Support\Admin;

use App\Models\Language;
use Illuminate\Database\Eloquent\Model;

/**
 * Resolves preferred admin form tab language, heading translation row, and language code for edit views.
 */
final class AdminEditHeadingLocale
{
    /**
     * @return array{
     *     preferredContentTabLanguageId: int,
     *     headingTranslation: Model|null,
     *     preferredContentTabLanguageCode: string|null
     * }
     */
    public function resolveFor(Model $translatable): array
    {
        $translatable->loadMissing(['translations.language']);

        $preferredId = Language::idForPreferredFormLocale();
        $translations = $translatable->translations;
        $headingTranslation = $translations->firstWhere('language_id', $preferredId)
            ?? $translations->first();

        $preferredContentTabLanguageCode = $headingTranslation?->language?->code
            ?? Language::query()->whereKey($preferredId)->value('code');

        return [
            'preferredContentTabLanguageId' => $preferredId,
            'headingTranslation' => $headingTranslation,
            'preferredContentTabLanguageCode' => $preferredContentTabLanguageCode,
        ];
    }

    public static function preferredContentTabLanguageId(): int
    {
        return Language::idForPreferredFormLocale();
    }
}
