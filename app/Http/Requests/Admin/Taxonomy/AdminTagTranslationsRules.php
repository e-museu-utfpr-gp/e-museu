<?php

namespace App\Http\Requests\Admin\Taxonomy;

use App\Models\Language;
use App\Models\Taxonomy\Tag;
use App\Support\Admin\AdminTranslatableNameFormRules;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

final class AdminTagTranslationsRules
{
    /**
     * @return array<string, mixed>
     */
    public static function rules(?Tag $tag = null, ?int $categoryId = null): array
    {
        $categoryId = $categoryId ?? (int) request()->input('category_id');

        $rules = AdminTranslatableNameFormRules::baseTranslationRules();
        $rules['category_id'] = 'required|integer|numeric|exists:tag_categories,id';
        $rules['validation'] = 'sometimes|boolean';

        $tagIdsInCategory = Tag::query()->where('tag_category_id', $categoryId)->pluck('id')->all();

        foreach (Language::forAdminContentForms() as $lang) {
            $c = $lang->code;
            $rules["translations.{$c}"] = ['nullable', 'array'];
            $ignoreId = $tag instanceof Tag
                ? $tag->translations()->where('language_id', $lang->id)->value('id')
                : null;

            $nameRules = [
                'nullable',
                'string',
                'max:200',
            ];

            $uniqueRule = Rule::unique('tag_translations', 'name')
                ->where('language_id', $lang->id)
                ->whereIn('tag_id', $tagIdsInCategory);
            if ($ignoreId !== null) {
                $uniqueRule = $uniqueRule->ignore($ignoreId);
            }
            $nameRules[] = $uniqueRule;

            $rules["translations.{$c}.name"] = $nameRules;
        }

        return $rules;
    }

    /**
     * @param  array<string, mixed>  $translations
     */
    public static function validateTranslationConsistency(Validator $validator, array $translations): void
    {
        AdminTranslatableNameFormRules::validateAtLeastOneNonEmptyName(
            $validator,
            $translations,
            'validation.tags.translations.at_least_one_locale'
        );
    }

    /**
     * @param  array<string, mixed>  $raw
     * @return array<string, mixed>
     */
    public static function normalizeEmptyStringsToNull(array $raw): array
    {
        return AdminTranslatableNameFormRules::normalizeEmptyNameStringsToNull($raw);
    }
}
