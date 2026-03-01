<?php

namespace App\Http\Requests\Proprietary;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class NewProprietaryRequest extends FormRequest
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
        $proprietaryId = null;
        if ($this->route()) {
            $proprietaryId = $this->route()->parameter('proprietary');
        }

        return [
            'full_name' => 'required|string|min:1|max:200',
            'contact' => [
                'required',
                'email:rfc,dns',
                'min:1',
                'max:200',
                Rule::unique('proprietaries')->ignore($proprietaryId),
            ],
            'blocked' => 'sometimes|boolean',
            'is_admin' => 'sometimes|boolean',
        ];
    }
}
