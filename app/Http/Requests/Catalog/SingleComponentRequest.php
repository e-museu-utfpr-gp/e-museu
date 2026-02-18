<?php

namespace App\Http\Requests\Catalog;

use Closure;
use Illuminate\Foundation\Http\FormRequest;

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
                function (string $attribute, mixed $value, Closure $fail): void {
                    if ((string) request('item_id') === (string) request('component_id')) {
                        $fail('O item e o componente precisam ser diferentes.');
                    }
                },
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
