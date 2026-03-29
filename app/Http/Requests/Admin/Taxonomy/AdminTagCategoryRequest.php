<?php

namespace App\Http\Requests\Admin\Taxonomy;

use App\Models\Language;
use App\Models\Taxonomy\TagCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminTagCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, string|array<int, string|\Illuminate\Validation\Rule>>
     */
    public function rules(): array
    {
        $formLangId = Language::idForPreferredFormLocale();
        $category = $this->route('tag_category');
        $ignoreTranslationId = $category instanceof TagCategory
            ? $category->translations()->where('language_id', $formLangId)->value('id')
            : null;

        return [
            'name' => [
                'required',
                'string',
                'min:1',
                'max:200',
                Rule::unique('tag_category_translations', 'name')
                    ->where('language_id', $formLangId)
                    ->ignore($ignoreTranslationId),
            ],
        ];
    }
}
