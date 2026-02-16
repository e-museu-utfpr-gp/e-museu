<?php

namespace App\Http\Requests\Taxonomy;

use Illuminate\Foundation\Http\FormRequest;

class TagRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'tags.*.category_id' => 'sometimes|required|integer|numeric|exists:categories,id',
            'tags.*.name' => 'sometimes|required|string|min:1|max:200'
        ];
    }
}
