<?php

namespace App\Http\Requests\Contracts;

use Illuminate\Validation\Validator;

/**
 * Admin forms that POST a `translations[locale][fields]` tree: normalize empty strings,
 * then run cross-locale consistency in {@see Validator::after()}.
 */
interface AdminTranslationsPayloadContract
{
    /**
     * @param  array<string, mixed>  $raw
     * @return array<string, mixed>
     */
    public static function normalizeEmptyStringsToNull(array $raw): array;

    /**
     * @param  array<string, mixed>  $translations
     */
    public static function validateTranslationConsistency(Validator $validator, array $translations): void;
}
