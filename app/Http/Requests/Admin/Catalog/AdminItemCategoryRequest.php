<?php

namespace App\Http\Requests\Admin\Catalog;

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
        return [
            'name' => [
                'required',
                'string',
                'min:1',
                'max:200',
                Rule::unique('item_categories')->ignore($this->route('item_category')),
            ],
        ];
    }
}
