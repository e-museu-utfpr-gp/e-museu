<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Validation\Rules\File;

class StoreItemRequest extends FormRequest
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
            'name' => 'required|string|min:1|max:200|unique:items',
            'date' => 'nullable|date',
            'description' => 'required|string|min:1|max:1000',
            'detail' => 'nullable|max:10000',
            'history' => 'nullable|max:100000',
            'section_id' => 'required|integer|numeric|exists:sections,id',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:10240',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O campo nome é obrigatório.',
            'description.required' => 'O campo descrição é obrigatório.',
            'section_id.required' => 'O campo categoria de item é obrigatório.',
            'image.required' => 'O campo imagem é obrigatório.',
            'image.mimes:jpeg,png,jpg,webp' => 'A extensão da imagem deve ser: jpeg, png ou webp.',
            'image.max:10240' => 'A imagem deve ter no máximo 10MB.',
        ];
    }
}
