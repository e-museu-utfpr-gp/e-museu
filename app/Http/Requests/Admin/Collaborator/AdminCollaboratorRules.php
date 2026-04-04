<?php

namespace App\Http\Requests\Admin\Collaborator;

use App\Enums\Collaborator\CollaboratorRole;
use Illuminate\Validation\Rule;

/**
 * Shared rules for {@see AdminStoreCollaboratorRequest} and {@see AdminUpdateCollaboratorRequest}.
 */
final class AdminCollaboratorRules
{
    /**
     * @param  mixed  $ignoreContactUnique  For update: route id or bound model; passed to
     *         {@see Rule::unique()->ignore()}.
     * @return array<string, mixed>
     */
    public static function rules(mixed $ignoreContactUnique = null): array
    {
        $contact = [
            'required',
            'email:rfc,dns',
            'min:1',
            'max:200',
        ];

        if ($ignoreContactUnique === null) {
            $contact[] = 'unique:collaborators,contact';
        } else {
            $contact[] = Rule::unique('collaborators')->ignore($ignoreContactUnique);
        }

        return [
            'full_name' => 'required|string|min:1|max:200',
            'contact' => $contact,
            'role' => ['required', Rule::enum(CollaboratorRole::class)],
            'blocked' => 'sometimes|boolean',
        ];
    }
}
