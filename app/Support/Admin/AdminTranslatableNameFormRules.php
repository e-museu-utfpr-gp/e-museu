<?php

namespace App\Support\Admin;

use App\Models\Language;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/**
 * Admin forms with one `name` field per content locale (category/tag translation tables).
 */
final class AdminTranslatableNameFormRules
{
    /**
     * @return array<string, mixed>
     */
    public static function baseTranslationRules(): array
    {
        return [
            'translations' => ['required', 'array'],
        ];
    }

    /**
     * @param  array<string, mixed>  $rules
     * @return array<string, mixed>
     */
    public static function withScopedUniqueNameColumns(
        array $rules,
        ?Model $parent,
        string $translationsTable,
        string $parentForeignKeyColumn
    ): array {
        foreach (Language::forAdminContentForms() as $lang) {
            $c = $lang->code;
            $rules["translations.{$c}"] = ['nullable', 'array'];
            $ignoreId = $parent !== null
                ? $parent->translations()->where('language_id', $lang->id)->value('id')
                : null;

            $nameRules = [
                'nullable',
                'string',
                'max:200',
            ];

            if ($parent !== null) {
                $uniqueRule = Rule::unique($translationsTable, 'name')
                    ->where('language_id', $lang->id)
                    ->where($parentForeignKeyColumn, $parent->id);
                if ($ignoreId !== null) {
                    $uniqueRule = $uniqueRule->ignore($ignoreId);
                }
                $nameRules[] = $uniqueRule;
            }

            $rules["translations.{$c}.name"] = $nameRules;
        }

        return $rules;
    }

    /**
     * @param  array<string, mixed>  $translations
     */
    public static function validateAtLeastOneNonEmptyName(
        Validator $validator,
        array $translations,
        string $translationMessageKey
    ): void {
        $hasAny = false;
        foreach (Language::forAdminContentForms() as $lang) {
            $code = $lang->code;
            $block = $translations[$code] ?? [];
            if (! is_array($block)) {
                continue;
            }
            $name = trim((string) ($block['name'] ?? ''));
            if ($name !== '') {
                $hasAny = true;
            }
        }

        if (! $hasAny) {
            $validator->errors()->add('translations', __($translationMessageKey));
        }
    }

    /**
     * @param  array<string, mixed>  $raw
     * @return array<string, mixed>
     */
    public static function normalizeEmptyNameStringsToNull(array $raw): array
    {
        foreach (Language::forAdminContentForms() as $lang) {
            $c = $lang->code;
            if (! isset($raw[$c]) || ! is_array($raw[$c])) {
                continue;
            }
            if (array_key_exists('name', $raw[$c]) && $raw[$c]['name'] === '') {
                $raw[$c]['name'] = null;
            }
        }

        return $raw;
    }
}
