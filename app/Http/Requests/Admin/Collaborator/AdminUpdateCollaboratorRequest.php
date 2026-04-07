<?php

namespace App\Http\Requests\Admin\Collaborator;

use Illuminate\Foundation\Http\FormRequest;

class AdminUpdateCollaboratorRequest extends FormRequest
{
    /**
     * @return array<string, string|array<int, string>>
     */
    public function rules(): array
    {
        return AdminCollaboratorRules::rules(
            $this->route()?->parameter('collaborator')
        );
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
