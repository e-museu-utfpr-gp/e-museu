<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Collaborator;

use App\Enums\Collaborator\CollaboratorRole;
use Illuminate\Validation\Rule;

/**
 * Shared rules for {@see AdminStoreCollaboratorRequest} and {@see AdminUpdateCollaboratorRequest}.
 */
final class AdminCollaboratorRules
{
    /**
     * @param  mixed  $ignoreEmailUnique  For update: route id or bound model; passed to
     *         {@see Rule::unique()->ignore()}.
     * @return array<string, mixed>
     */
    public static function rules(mixed $ignoreEmailUnique = null): array
    {
        $email = [
            'required',
            'email:rfc',
            'min:1',
            'max:200',
        ];

        if ($ignoreEmailUnique === null) {
            $email[] = 'unique:collaborators,email';
        } else {
            $email[] = Rule::unique('collaborators')->ignore($ignoreEmailUnique);
        }

        return [
            'full_name' => 'required|string|min:1|max:200',
            'email' => $email,
            'role' => ['required', Rule::enum(CollaboratorRole::class)],
            'blocked' => 'sometimes|boolean',
            'last_email_verification_at' => 'nullable|date',
        ];
    }
}
