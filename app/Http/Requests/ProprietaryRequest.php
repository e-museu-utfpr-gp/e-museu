<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProprietaryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, string>
     */
    public function rules(): array
    {
        return [
            'full_name' => 'required|string|min:1|max:200',
            'contact' => 'required|email:rfc,dns|min:1|max:200',
            'is_admin' => 'sometimes|boolean',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'full_name.required' => 'O campo Nome Completo é obrigatório.',
            'contact.required' => 'O campo Email é obrigatório.',
            'is_admin' => 'O campo Administrador é obrigatório.',
        ];
    }
}
