<?php

namespace App\Http\Requests\Admin\Catalog;

use App\Models\Catalog\ItemCategory;
use App\Models\Language;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminItemCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $formLangId = Language::idForPreferredFormLocale();
        $category = $this->route('item_category');
        $ignoreTranslationId = $category instanceof ItemCategory
            ? $category->translations()->where('language_id', $formLangId)->value('id')
            : null;

        $nameRules = [
            'required',
            'string',
            'min:1',
            'max:200',
        ];
        if ($category instanceof ItemCategory) {
            $nameRules[] = Rule::unique('item_category_translations', 'name')
                ->where('language_id', $formLangId)
                ->where('item_category_id', $category->id)
                ->ignore($ignoreTranslationId);
        }

        return [
            'name' => $nameRules,
        ];
    }
}
