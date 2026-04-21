<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Catalog;

use Illuminate\Foundation\Http\FormRequest;

class AdminSingleComponentRequest extends FormRequest
{
    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'item_id' => 'required|integer|numeric|exists:items,id',
            'component_id' => CatalogRelationIdRules::componentItemIdMustDifferFromParentItem(),
            'validation' => 'sometimes|boolean',
        ];
    }
}
