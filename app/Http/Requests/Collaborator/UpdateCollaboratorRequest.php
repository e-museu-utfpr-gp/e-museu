<?php

namespace App\Http\Requests\Collaborator;

use App\Enums\CollaboratorRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCollaboratorRequest extends FormRequest
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
        $collaboratorId = null;
        if ($this->route()) {
            $collaboratorId = $this->route()->parameter('collaborator');
        }

        return [
            'full_name' => 'required|string|min:1|max:200',
            'contact' => [
                'required',
                'email:rfc,dns',
                'min:1',
                'max:200',
                Rule::unique('collaborators')->ignore($collaboratorId),
            ],
            'role' => ['required', Rule::enum(CollaboratorRole::class)],
            'blocked' => 'sometimes|boolean',
        ];
    }
}
