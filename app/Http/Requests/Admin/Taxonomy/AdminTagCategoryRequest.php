<?php

namespace App\Http\Requests\Admin\Taxonomy;

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
        return [
            'name' => [
                'required',
                'string',
                'min:1',
                'max:200',
                Rule::unique('tag_categories')->ignore($this->route('tag_category')),
            ],
        ];
    }
}
