<?php

namespace App\Http\Requests\Admin\Collaborator;

use Illuminate\Foundation\Http\FormRequest;

class AdminStoreCollaboratorRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return AdminCollaboratorRules::rules();
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.unique' => __('app.collaborator.email_unique'),
        ];
    }
}
