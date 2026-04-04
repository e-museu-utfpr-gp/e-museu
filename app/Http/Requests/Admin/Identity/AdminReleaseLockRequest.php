<?php

namespace App\Http\Requests\Admin\Identity;

use Illuminate\Foundation\Http\FormRequest;

class AdminReleaseLockRequest extends FormRequest
{
    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'type' => 'required|string|in:items,item-categories,tag-categories,tags,collaborators,extras',
            'id' => 'required|integer|min:1',
        ];
    }
}
