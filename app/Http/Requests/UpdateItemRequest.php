<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, string|array<int, string>>
     */
    public function rules(): array
    {
        $route = $this->route();
        $item = $route ? $route->parameter('item') : null;
        $itemId = $item instanceof \App\Models\Catalog\Item ? $item->id : null;

        return [
            'name' => [
                'required',
                'string',
                'min:1',
                'max:200',
                Rule::unique('items')->ignore($itemId),
            ],
            'date' => 'date',
            'description' => 'required|string|min:1|max:1000',
            'detail' => 'max:10000',
            'history' => 'max:50000',
            'section_id' => 'required|integer|numeric|exists:sections,id',
            'proprietary_id' => 'required|integer|numeric|exists:proprietaries,id',
            'identification_code' => 'required|string|min:1|max:50',
            'validation' => 'required|boolean',
            'image' => 'sometimes|image|max:10240',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'O campo nome é obrigatório.',
            'data.required' => 'O campo data é obrigatório.',
            'description.required' => 'O campo descrição é obrigatório.',
            'section_id.required' => 'O campo categoria de item é obrigatório.',
            'validation.required' => 'O campo validado é obrigatório.',
            'identification_code.required' => 'O campo código de identificação é obrigatório.',
            'proprietary_id.required' => 'O campo proprietário é obrigatório.',
        ];
    }
}
