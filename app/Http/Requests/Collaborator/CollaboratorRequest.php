<?php

namespace App\Http\Requests\Collaborator;

use Illuminate\Foundation\Http\FormRequest;

class CollaboratorRequest extends FormRequest
{
    /**
     * Rules for public contribution (external users). Email must not belong to an INTERNAL collaborator.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return PublicCollaboratorRules::rules();
    }
}
