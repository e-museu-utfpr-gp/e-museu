<?php

namespace App\Support\Content;

use App\Enums\Content\ContentLanguage;
use Illuminate\Support\Collection;

/**
 * Ordered content locale codes for SQL FIELD() and PHP resolution (matches across the app).
 *
 * Resolution order: active locale → universal (locale-agnostic copy) → config fallback → other seeded locales.
 */
final class ContentLocaleFallback
{
    /**
     * Map `app()->getLocale()` to a value that exists in `languages.code` when possible.
     */
    public static function normalizedAppLocaleCode(): string
    {
        $locale = app()->getLocale();
        if ($locale === 'pt' || ($locale !== 'pt_BR' && str_starts_with($locale, 'pt'))) {
            return ContentLanguage::PT_BR->value;
        }
        if (str_starts_with($locale, 'en')) {
            return ContentLanguage::EN->value;
        }

        $known = [
            ContentLanguage::PT_BR->value,
            ContentLanguage::EN->value,
            ContentLanguage::UNIVERSAL->value,
        ];
        if (in_array($locale, $known, true)) {
            return $locale;
        }

        return ContentLanguage::defaultForForms()->value;
    }

    /**
     * @return list<string>
     */
    public static function orderedCodes(): array
    {
        /** @var list<string> $codes */
        $codes = array_values(
            collect([
                self::normalizedAppLocaleCode(),
                ContentLanguage::UNIVERSAL->value,
                config('app.fallback_locale'),
                ...ContentLanguage::orderedNonUniversalLocales(),
            ])
                ->filter(fn ($c) => is_string($c) && $c !== '')
                ->unique()
                ->values()
                ->all()
        );

        return $codes;
    }

    /**
     * Comma-separated quoted codes for MySQL FIELD(l.code, ...).
     */
    public static function fieldListSql(): string
    {
        return Collection::make(self::orderedCodes())
            ->map(fn (string $c) => "'" . str_replace("'", "''", $c) . "'")
            ->implode(',');
    }
}
