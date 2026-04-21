<?php

declare(strict_types=1);

namespace App\Enums\Content;

/**
 * Content locale codes stored in `languages.code` (varchar + seed).
 * Must match rows seeded in the `languages` migration.
 *
 * Adding a language requires updating this enum, seeds, `lang/{code}/`, and (for client strings) `lang/js` + `i18n.js`;
 * see README “Adding a catalog / content language”.
 */
enum ContentLanguage: string
{
    case PT_BR = 'pt_BR';
    case EN = 'en';
    case UNIVERSAL = 'universal';

    /** Default locale used when saving from admin / contribution forms. */
    public static function defaultForForms(): self
    {
        return self::PT_BR;
    }

    /**
     * Order for content-language selects (admin + public contribute). Independent of `languages.id`.
     *
     * @return list<string>
     */
    public static function orderedCodesForAdminForms(): array
    {
        return [
            self::UNIVERSAL->value,
            self::PT_BR->value,
            self::EN->value,
        ];
    }

    /**
     * Translatable locales to try after active locale, universal, and `config('app.fallback_locale')`.
     * Stable order for SQL FIELD() / PHP resolution (see ContentLocaleFallback::orderedCodes()).
     *
     * @return list<string>
     */
    public static function orderedNonUniversalLocales(): array
    {
        $priority = [self::EN, self::PT_BR];
        $seen = [];
        $out = [];
        foreach ($priority as $case) {
            $seen[$case->value] = true;
            $out[] = $case->value;
        }
        foreach (self::cases() as $case) {
            if ($case === self::UNIVERSAL || isset($seen[$case->value])) {
                continue;
            }
            $out[] = $case->value;
        }

        return $out;
    }
}
