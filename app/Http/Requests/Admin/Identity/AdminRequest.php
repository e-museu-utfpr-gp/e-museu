<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Identity;

use Illuminate\Foundation\Http\FormRequest;

class AdminRequest extends FormRequest
{
    /**
     * @return array<string, string>
     */
    public function rules(): array
    {
        return [
            'username' => 'required|string|min:1|max:100|unique:admins',
            'password' => 'required|string|min:1|max:100',
        ];
    }
}
