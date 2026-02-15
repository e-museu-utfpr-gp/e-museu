<?php

namespace App\Http\Requests\Catalog;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\DifferentIds;

class SingleComponentRequest extends FormRequest
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
            'component_id' => [
                'sometimes',
                'integer',
                'numeric',
                'exists:items,id',
                new DifferentIds([request('item_id'), request('component_id')]),
            ],
            'validation' => 'sometimes|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'item_id.required' => 'O campo id do item principal é obrigatório.',
            'component_id.required' => 'O campo id do componente é obrigatório.',
            'validation.required' => 'O campo validação é obrigatório.',
        ];
    }
}
