<?php

namespace App\Http\Requests\Catalog;

use Closure;
use Illuminate\Foundation\Http\FormRequest;

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
            'tag_id' => [
                'required',
                'integer',
                'numeric',
                'exists:tags,id',
                function (string $attribute, mixed $value, Closure $fail): void {
                    if ((string) request('item_id') === (string) request('tag_id')) {
                        $fail(__('validation.catalog.item_tag_different'));
                    }
                },
            ],
            'validation' => 'required|boolean',
        ];
    }
}
