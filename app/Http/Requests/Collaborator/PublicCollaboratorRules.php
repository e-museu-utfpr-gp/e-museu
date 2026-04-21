<?php

declare(strict_types=1);

namespace App\Http\Requests\Collaborator;

use App\Enums\Collaborator\CollaboratorRole;
use App\Models\Collaborator\Collaborator;

/**
 * Shared rules for public catalog contribution ({@see CollaboratorRequest},
 * {@see \App\Http\Requests\Catalog\SingleExtraRequest},
 * {@see \App\Http\Requests\Catalog\ItemContributionValidator}).
 */
final class PublicCollaboratorRules
{
    /**
     * @return array<string, mixed>
     */
    public static function rules(): array
    {
        return [
            'full_name' => 'required|string|min:1|max:200',
            'email' => [
                'required',
                'email:rfc',
                'min:1',
                'max:200',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if (! is_string($value)) {
                        return;
                    }
                    $normalized = mb_strtolower(trim($value));
                    if ($normalized === '') {
                        return;
                    }
                    if (
                        Collaborator::query()
                            ->where('role', CollaboratorRole::INTERNAL)
                            ->whereRaw('LOWER(TRIM(email)) = ?', [$normalized])
                            ->exists()
                    ) {
                        $fail(__('app.collaborator.email_reserved_for_internal'));
                    }
                },
            ],
        ];
    }
}
