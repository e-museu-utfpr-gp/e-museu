<?php

namespace App\Http\Requests\Admin\Taxonomy;

use App\Models\Language;
use App\Models\Taxonomy\Tag;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminSingleTagRequest extends FormRequest
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
        $categoryId = (int) $this->input('category_id');
        $tag = $this->route('tag');
        $ignoreTranslationId = $tag instanceof Tag
            ? $tag->translations()->where('language_id', $formLangId)->value('id')
            : null;

        return [
            'name' => [
                'required',
                'string',
                'min:1',
                'max:200',
                Rule::unique('tag_translations', 'name')
                    ->where('language_id', $formLangId)
                    ->whereIn(
                        'tag_id',
                        Tag::query()->where('tag_category_id', $categoryId)->pluck('id')->all()
                    )
                    ->ignore($ignoreTranslationId),
            ],
            'category_id' => 'required|integer|numeric|exists:tag_categories,id',
            'validation' => 'sometimes|boolean',
        ];
    }
}
