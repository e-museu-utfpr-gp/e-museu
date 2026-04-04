<?php

namespace App\Http\Requests\Catalog;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Public item contribution: each extra row is only {@code extras[n][info]}.
 * {@see \App\Services\Catalog\ExtraService::createForItem()} sets {@code collaborator_id} and {@code item_id}.
 */
class ExtraRequest extends FormRequest
{
    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'extras.*.info' => 'sometimes|required|string|min:1|max:10000',
        ];
    }
}
