<?php

declare(strict_types=1);

namespace App\Support\Catalog;

use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

/**
 * Maps public catalog contribution result statuses to validation errors for form re-display.
 *
 * When adding a new early-exit status in
 * {@see \App\Actions\Catalog\StoreItemContribution\Concerns\PersistsContribution::persistContribution()},
 * add a matching arm here so users see a field error instead of an HTTP 500.
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
            default => self::throwUnknownStatus($result),
        };
    }

    /**
     * @param  array{status?: string}  $result
     */
    private static function throwUnknownStatus(array $result): never
    {
        Log::warning('Unknown public catalog contribution status.', [
            'status' => $result['status'] ?? null,
        ]);

        throw ValidationException::withMessages([
            'email' => __('app.catalog.item.contribution_unexpected'),
        ]);
    }
}
