<?php

namespace App\Http\Requests\Admin\Catalog;

use App\Models\Catalog\ItemCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class AdminItemCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $raw = $this->input('translations', []);
        if (! is_array($raw)) {
            return;
        }
        $this->merge([
            'translations' => AdminItemCategoryTranslationsRules::normalizeEmptyStringsToNull($raw),
        ]);
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            AdminItemCategoryTranslationsRules::validateTranslationConsistency(
                $validator,
                $this->input('translations', [])
            );
        });
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $category = $this->route('item_category');

        return AdminItemCategoryTranslationsRules::rules($category instanceof ItemCategory ? $category : null);
    }
}
