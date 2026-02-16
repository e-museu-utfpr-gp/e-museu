<?php

namespace App\Http\Requests\Catalog;

use Illuminate\Foundation\Http\FormRequest;

class ExtraRequest extends FormRequest
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
            'extras.*.info' => 'sometimes|required|string|min:1|max:10000',
            'extras.*.proprietary_id' => 'sometimes|required|integer|numeric|exists:proprietaries,id',
            'extras.*.item_id' => 'sometimes|required|integer|numeric|exists:items,id',
        ];
    }
}
