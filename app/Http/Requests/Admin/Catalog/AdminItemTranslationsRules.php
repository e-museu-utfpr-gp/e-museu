<?php

namespace App\Http\Requests\Admin\Catalog;

use App\Models\Catalog\Item;
use App\Models\Language;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

final class AdminItemTranslationsRules
{
    /**
     * @return array<string, mixed>
     */
    public static function rules(?Item $item = null): array
    {
        $rules = [
            'translations' => ['required', 'array'],
        ];

        foreach (Language::forAdminContentForms() as $lang) {
            $c = $lang->code;
            $rules["translations.{$c}"] = ['nullable', 'array'];
            $ignoreId = $item instanceof Item
                ? $item->translations()->where('language_id', $lang->id)->value('id')
                : null;

            $nameRules = [
                'nullable',
                'string',
                'max:200',
            ];
            $uniqueRule = Rule::unique('item_translations', 'name')
                ->where('language_id', $lang->id);
            if ($ignoreId !== null) {
                $uniqueRule = $uniqueRule->ignore($ignoreId);
            }
            $nameRules[] = $uniqueRule;

            $rules["translations.{$c}.name"] = $nameRules;
            $rules["translations.{$c}.description"] = ['nullable', 'string', 'max:1000'];
            $rules["translations.{$c}.detail"] = ['nullable', 'string', 'max:10000'];
            $rules["translations.{$c}.history"] = ['nullable', 'string', 'max:100000'];
        }

        return $rules;
    }

    /**
     * @param  array<string, mixed>  $translations
     */
    public static function validateTranslationConsistency(Validator $validator, array $translations): void
    {
        $hasComplete = false;

        foreach (Language::forAdminContentForms() as $lang) {
            $code = $lang->code;
            $block = $translations[$code] ?? [];
            if (! is_array($block)) {
                continue;
            }

            $name = trim((string) ($block['name'] ?? ''));
            $desc = trim((string) ($block['description'] ?? ''));
            $detail = trim((string) ($block['detail'] ?? ''));
            $hist = trim((string) ($block['history'] ?? ''));
            $any = $name !== '' || $desc !== '' || $detail !== '' || $hist !== '';

            if ($any && ($name === '' || $desc === '')) {
                $validator->errors()->add(
                    "translations.{$code}.description",
                    __('validation.items.translations.incomplete_for_locale', [
                        'locale' => $lang->name ?? $code,
                    ])
                );
            }

            if ($name !== '' && $desc !== '') {
                $hasComplete = true;
            }
        }

        if (! $hasComplete) {
            $validator->errors()->add(
                'translations',
                __('validation.items.translations.at_least_one_locale')
            );
        }
    }

    /**
     * @param  array<string, mixed>  $raw
     * @return array<string, mixed>
     */
    public static function normalizeEmptyStringsToNull(array $raw): array
    {
        foreach (Language::forAdminContentForms() as $lang) {
            $c = $lang->code;
            if (! isset($raw[$c]) || ! is_array($raw[$c])) {
                continue;
            }
            foreach (['name', 'description', 'detail', 'history'] as $f) {
                if (array_key_exists($f, $raw[$c]) && $raw[$c][$f] === '') {
                    $raw[$c][$f] = null;
                }
            }
        }

        return $raw;
    }
}
