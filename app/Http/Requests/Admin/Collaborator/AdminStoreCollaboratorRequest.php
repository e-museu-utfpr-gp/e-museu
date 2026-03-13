<?php

namespace App\Http\Requests\Admin\Collaborator;

use App\Enums\Collaborator\CollaboratorRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminStoreCollaboratorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'full_name' => 'required|string|min:1|max:200',
            'contact' => 'required|email:rfc,dns|min:1|max:200|unique:collaborators,contact',
            'role' => ['required', Rule::enum(CollaboratorRole::class)],
            'blocked' => 'sometimes|boolean',
        ];
    }
}
