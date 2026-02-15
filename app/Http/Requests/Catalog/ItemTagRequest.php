<?php

namespace App\Http\Requests\Catalog;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\DifferentIds;

class ItemTagRequest extends FormRequest
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
            'item_id' => 'required|integer|numeric|exists:items,id',
            'tag_id' => 'required|integer|numeric|exists:tags,id',
            'validation' => 'required|boolean'
        ];
    }

    public function messages(): array
    {
        return [
            'item_id.required' => 'O campo id do item principal é obrigatório.',
            'tag_id.required' => 'O campo id da etiqueta é obrigatório.',
            'validation.required' => 'O campo validação é obrigatório.',
        ];
    }
}
