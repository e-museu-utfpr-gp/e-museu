<?php

namespace App\Http\Requests\Identity;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
            'username' => 'required|string|min:1|max:100|unique:users',
            'password' => 'required|string|min:1|max:100',
        ];
    }
}
