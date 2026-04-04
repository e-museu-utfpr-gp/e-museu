<?php

namespace App\Http\Requests\Admin\Catalog;

use App\Models\Catalog\Item;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class AdminUpdateItemRequest extends FormRequest
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
     * @return array<string, string|array<int, string|\Illuminate\Validation\Rules\Unique>>
     */
    public function rules(): array
    {
        $route = $this->route();
        $item = $route ? $route->parameter('item') : null;

        return array_merge(AdminItemTranslationsRules::rules($item instanceof Item ? $item : null), [
            'date' => 'nullable|date',
            'category_id' => 'required|integer|numeric|exists:item_categories,id',
            'collaborator_id' => 'required|integer|numeric|exists:collaborators,id',
            'identification_code' => 'required|string|min:1|max:50',
            'validation' => 'required|boolean',
            'image' => 'sometimes|image|max:10240',
            'gallery_images' => 'sometimes|array',
            'gallery_images.*' => 'image|mimes:jpeg,png,jpg,webp|max:10240',
            'delete_image_ids' => 'sometimes|array',
            'delete_image_ids.*' => 'integer|exists:item_images,id',
            'set_cover_image_id' => 'sometimes|nullable|integer|exists:item_images,id',
        ]);
    }
}
