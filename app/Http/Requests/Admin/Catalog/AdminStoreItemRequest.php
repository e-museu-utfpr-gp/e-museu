<?php

namespace App\Http\Requests\Admin\Catalog;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class AdminStoreItemRequest extends FormRequest
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
            'translations' => AdminItemTranslationsRules::normalizeEmptyStringsToNull($raw),
        ]);
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            AdminItemTranslationsRules::validateTranslationConsistency(
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
        return array_merge(AdminItemTranslationsRules::rules(null), [
            'date' => 'nullable|date',
            'category_id' => 'required|integer|numeric|exists:item_categories,id',
            'collaborator_id' => 'required|integer|numeric|exists:collaborators,id',
            'validation' => 'required|boolean',
            'cover_image' => 'required|image|mimes:jpeg,png,jpg,webp|max:10240',
            'gallery_images' => 'sometimes|array',
            'gallery_images.*' => 'image|mimes:jpeg,png,jpg,webp|max:10240',
        ]);
    }
}
