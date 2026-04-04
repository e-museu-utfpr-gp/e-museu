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
}
