<?php

namespace App\Http\Requests\Admin\Catalog;

use App\Models\Catalog\Item;
use App\Models\Language;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminUpdateItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, string|array<int, string>>
     */
    public function rules(): array
    {
        $route = $this->route();
        $item = $route ? $route->parameter('item') : null;
        $formLangId = Language::idForPreferredFormLocale();
        $ignoreTranslationId = $item instanceof Item
            ? $item->translations()->where('language_id', $formLangId)->value('id')
            : null;

        $nameRules = [
            'required',
            'string',
            'min:1',
            'max:200',
        ];
        if ($item instanceof Item) {
            $nameRules[] = Rule::unique('item_translations', 'name')
                ->where('language_id', $formLangId)
                ->where('item_id', $item->id)
                ->ignore($ignoreTranslationId);
        }

        return [
            'name' => $nameRules,
            'date' => 'nullable|date',
            'description' => 'required|string|min:1|max:1000',
            'detail' => 'max:10000',
            'history' => 'max:50000',
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
        ];
    }
}
