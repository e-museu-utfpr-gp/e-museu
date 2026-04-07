<?php

namespace App\Http\Requests\Admin\Taxonomy;

use App\Http\Requests\Contracts\AdminTranslationsPayloadContract;
use App\Models\Taxonomy\TagCategory;
use App\Support\Admin\AdminTranslatableNameFormRules;
use Illuminate\Validation\Validator;

final class AdminTagCategoryTranslationsRules implements AdminTranslationsPayloadContract
{
    /**
     * @return array<string, mixed>
     */
    public static function rules(?TagCategory $tagCategory = null): array
    {
        $rules = AdminTranslatableNameFormRules::baseTranslationRules();

        return AdminTranslatableNameFormRules::withScopedUniqueNameColumns(
            $rules,
            $tagCategory,
            'tag_category_translations',
            'tag_category_id'
        );
    }

    /**
     * @param  array<string, mixed>  $translations
     */
    public static function validateTranslationConsistency(Validator $validator, array $translations): void
    {
        AdminTranslatableNameFormRules::validateAtLeastOneNonEmptyName(
            $validator,
            $translations,
            'validation.tag_categories.translations.at_least_one_locale'
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
