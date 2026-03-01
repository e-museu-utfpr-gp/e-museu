<?php

namespace App\Http\Requests\Proprietary;

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
}
