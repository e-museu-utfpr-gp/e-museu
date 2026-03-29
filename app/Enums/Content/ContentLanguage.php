<?php

namespace App\Enums\Content;

/**
 * Content locale codes stored in `languages.code` (varchar + seed).
 * Must match rows seeded in the `languages` migration.
 */
enum ContentLanguage: string
{
    case PT_BR = 'pt_BR';
    case EN = 'en';
    case NEUTRAL = 'neutral';

    /** Default locale used when saving from admin / contribution forms. */
    public static function defaultForForms(): self
    {
        return self::PT_BR;
    }

    /**
     * Translatable locales to try after active locale, neutral, and `config('app.fallback_locale')`.
     * Stable order for SQL FIELD() / PHP resolution (see ContentLocaleFallback::orderedCodes()).
     *
     * @return list<string>
     */
    public static function orderedNonNeutralLocales(): array
    {
        $priority = [self::EN, self::PT_BR];
        $seen = [];
        $out = [];
        foreach ($priority as $case) {
            $seen[$case->value] = true;
            $out[] = $case->value;
        }
        foreach (self::cases() as $case) {
            if ($case === self::NEUTRAL || isset($seen[$case->value])) {
                continue;
            }
            $out[] = $case->value;
        }

        return $out;
    }
}
