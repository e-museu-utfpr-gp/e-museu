<?php

namespace App\Http\Requests\Admin\Catalog;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Admin CRUD for extras — same field rules as {@see \App\Http\Requests\Catalog\SingleExtraRequest},
 * separate class to avoid coupling public flows to admin changes.
 */
class AdminStoreExtraRequest extends FormRequest
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
            'info' => 'required|string|min:1|max:10000',
            'item_id' => 'required|integer|numeric|exists:items,id',
            'collaborator_id' => 'sometimes|integer|numeric|exists:collaborators,id',
            'validation' => 'sometimes|boolean',
        ];
    }
}
