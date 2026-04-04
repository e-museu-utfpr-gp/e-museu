<?php

namespace App\Http\Requests\Admin\Taxonomy;

use App\Models\Taxonomy\TagCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class AdminTagCategoryRequest extends FormRequest
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
            'translations' => AdminTagCategoryTranslationsRules::normalizeEmptyStringsToNull($raw),
        ]);
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            AdminTagCategoryTranslationsRules::validateTranslationConsistency(
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
        $category = $this->route('tag_category');

        return AdminTagCategoryTranslationsRules::rules($category instanceof TagCategory ? $category : null);
    }
}
