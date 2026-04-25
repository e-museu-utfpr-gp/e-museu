<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Catalog;

use App\Http\Requests\Contracts\AdminTranslationsPayloadContract;
use App\Models\Catalog\ItemCategory;
use App\Support\Admin\AdminTranslatableNameFormRules;
use Illuminate\Validation\Validator;

final class AdminItemCategoryTranslationsRules implements AdminTranslationsPayloadContract
{
    /**
     * @return array<string, mixed>
     */
    public static function rules(?ItemCategory $itemCategory = null): array
    {
        $rules = AdminTranslatableNameFormRules::baseTranslationRules();

        return AdminTranslatableNameFormRules::withScopedUniqueNameColumns(
            $rules,
            $itemCategory,
            'item_category_translations',
            'item_category_id'
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
            'validation.item_categories.translations.at_least_one_locale'
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
