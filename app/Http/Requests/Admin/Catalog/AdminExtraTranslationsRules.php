<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Catalog;

use App\Http\Requests\Contracts\AdminTranslationsPayloadContract;
use App\Models\Language;
use Illuminate\Validation\Validator;

final class AdminExtraTranslationsRules implements AdminTranslationsPayloadContract
{
    /**
     * @return array<string, mixed>
     */
    public static function rules(): array
    {
        $rules = [
            'translations' => ['required', 'array'],
            'item_id' => 'required|integer|numeric|exists:items,id',
            'collaborator_id' => 'required|integer|numeric|exists:collaborators,id',
            'validation' => 'sometimes|boolean',
        ];

        foreach (Language::forCatalogContentForms() as $lang) {
            $c = $lang->code;
            $rules["translations.{$c}"] = ['nullable', 'array'];
            $rules["translations.{$c}.info"] = ['nullable', 'string', 'max:10000'];
        }

        return $rules;
    }

    /**
     * @param  array<string, mixed>  $translations
     */
    public static function validateTranslationConsistency(Validator $validator, array $translations): void
    {
        $hasAny = false;
        foreach (Language::forCatalogContentForms() as $lang) {
            $code = $lang->code;
            $block = $translations[$code] ?? [];
            if (! is_array($block)) {
                continue;
            }
            $info = trim((string) ($block['info'] ?? ''));
            if ($info !== '') {
                $hasAny = true;
            }
        }

        if (! $hasAny) {
            $validator->errors()->add(
                'translations',
                __('validation.extras.translations.at_least_one_locale')
            );
        }
    }

    /**
     * @param  array<string, mixed>  $raw
     * @return array<string, mixed>
     */
    public static function normalizeEmptyStringsToNull(array $raw): array
    {
        foreach (Language::forCatalogContentForms() as $lang) {
            $c = $lang->code;
            if (! isset($raw[$c]) || ! is_array($raw[$c])) {
                continue;
            }
            if (array_key_exists('info', $raw[$c]) && $raw[$c]['info'] === '') {
                $raw[$c]['info'] = null;
            }
        }

        return $raw;
    }
}
