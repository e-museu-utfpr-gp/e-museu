<?php

namespace App\Http\Requests\Collaborator;

use App\Enums\CollaboratorRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCollaboratorRequest extends FormRequest
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
