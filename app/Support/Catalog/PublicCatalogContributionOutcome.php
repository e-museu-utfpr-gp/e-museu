<?php

namespace App\Support\Catalog;

use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

/**
 * Maps public catalog contribution result statuses to validation errors for form re-display.
 */
final class PublicCatalogContributionOutcome
{
    /**
     * @param  array{status?: string, item?: \App\Models\Catalog\Item}  $result
     */
    public static function throwUnlessOk(array $result): void
    {
        if (($result['status'] ?? '') === 'ok') {
            return;
        }

        match ($result['status'] ?? '') {
            'internal_blocked' => throw ValidationException::withMessages([
                'email' => __('app.collaborator.email_reserved_for_internal'),
            ]),
            'collaborator_blocked' => throw ValidationException::withMessages([
                'email' => __('app.collaborator.blocked_from_registering'),
            ]),
            'email_unverified' => throw ValidationException::withMessages([
                'email' => __('app.collaborator.email_must_verify_before_contribution'),
            ]),
            'collaborator_invalid' => throw ValidationException::withMessages([
                'collaborator_id' => __('validation.exists', ['attribute' => 'collaborator id']),
            ]),
            default => throw new InvalidArgumentException(
                'Unknown public catalog contribution status: ' . (string) ($result['status'] ?? '')
            ),
        };
    }
}
