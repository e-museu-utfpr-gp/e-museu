<?php

declare(strict_types=1);

namespace App\Support\Catalog;

use App\Enums\Collaborator\CollaboratorRole;
use App\Models\Collaborator\Collaborator;
use App\Services\Collaborator\CollaboratorService;

/**
 * Resolves collaborator eligibility for a public catalog item contribution and applies post-verify name updates.
 */
final class PublicContributionCollaboratorSupport
{
    public function __construct(
        private readonly CollaboratorService $collaboratorService,
    ) {
    }

    /**
     * @param  array<string, mixed>  $collaboratorData
     */
    public function applySubmittedFullNameAfterVerifiedContribution(
        Collaborator $collaborator,
        array $collaboratorData,
    ): void {
        $this->collaboratorService->applySubmittedFullNameAfterVerifiedContribution(
            $collaborator,
            (string) ($collaboratorData['full_name'] ?? ''),
        );
    }

    /**
     * @param  array<string, mixed>  $collaboratorData
     * @return array{
     *     status: 'ok',
     *     collaborator: Collaborator,
     * }|array{
     *     status: 'internal_blocked'|'collaborator_blocked'|'email_unverified',
     *     collaborator: null,
     * }
     */
    public function resolveForContribution(array $collaboratorData): array
    {
        $collaborator = $this->collaboratorService->findCollaboratorByEmailForPublicLookup(
            (string) ($collaboratorData['email'] ?? ''),
        );
        if ($collaborator === null) {
            return [
                'status' => 'email_unverified',
                'collaborator' => null,
            ];
        }

        if ($collaborator->role === CollaboratorRole::INTERNAL) {
            return [
                'status' => 'internal_blocked',
                'collaborator' => null,
            ];
        }

        if ($collaborator->blocked === true) {
            return [
                'status' => 'collaborator_blocked',
                'collaborator' => null,
            ];
        }

        $gate = $this->collaboratorService->publicContributionCollaboratorGate($collaborator, $collaboratorData);
        if ($gate === 'email_unverified') {
            return [
                'status' => 'email_unverified',
                'collaborator' => null,
            ];
        }

        return [
            'status' => 'ok',
            'collaborator' => $collaborator,
        ];
    }
}
