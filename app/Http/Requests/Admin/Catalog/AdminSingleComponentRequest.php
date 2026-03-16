<?php

namespace App\Http\Requests\Admin\Catalog;

use Closure;
use Illuminate\Foundation\Http\FormRequest;

class AdminSingleComponentRequest extends FormRequest
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
            'item_id' => 'required|integer|numeric|exists:items,id',
            'component_id' => [
                'sometimes',
                'integer',
                'numeric',
                'exists:items,id',
                function (string $attribute, mixed $value, Closure $fail): void {
                    if ((string) request('item_id') === (string) request('component_id')) {
                        $fail(__('validation.catalog.item_component_different'));
                    }
                },
            ],
            'validation' => 'sometimes|boolean',
        ];
    }
}
