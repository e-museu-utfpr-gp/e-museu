<?php

declare(strict_types=1);

namespace App\Actions\Catalog\StoreItemContribution\Concerns;

use App\Enums\Collaborator\CollaboratorRole;
use App\Models\Collaborator\Collaborator;
use Illuminate\Database\QueryException;
use LogicException;

trait ResolvesCollaboratorForContribution
{
    /**
     * @param  array<string, mixed>  $collaboratorData
     * @return Collaborator|array{status: 'internal_blocked'|'collaborator_blocked'|'email_unverified'}
     */
    protected function collaboratorForContributionOrEarlyStatus(array $collaboratorData): Collaborator|array
    {
        $resolution = $this->collaboratorService->resolveForPublicCatalogContribution($collaboratorData);

        if (
            $resolution['status'] === 'email_unverified'
            && ! (bool) config('mail.public_contribution_email_verification_enabled')
        ) {
            $created = $this->findOrCreateExternalCollaboratorForPublicContribution($collaboratorData);
            if ($created !== null) {
                $resolution = $this->collaboratorService->resolveForPublicCatalogContribution($collaboratorData);
            }
        }

        if ($resolution['status'] !== 'ok') {
            return ['status' => $resolution['status']];
        }
        $collaborator = $resolution['collaborator'];
        if (! $collaborator instanceof Collaborator) {
            throw new LogicException(
                'resolveForPublicCatalogContribution returned status ok without a Collaborator instance.',
            );
        }

        return $collaborator;
    }

    /**
     * @param  array<string, mixed>  $collaboratorData
     */
    private function findOrCreateExternalCollaboratorForPublicContribution(array $collaboratorData): ?Collaborator
    {
        $email = trim((string) ($collaboratorData['email'] ?? ''));
        $fullName = trim((string) ($collaboratorData['full_name'] ?? ''));
        if ($email === '' || $fullName === '') {
            return null;
        }

        try {
            return Collaborator::create([
                'email' => $email,
                'full_name' => $fullName,
                'role' => CollaboratorRole::EXTERNAL,
                'blocked' => false,
            ]);
        } catch (QueryException $e) {
            if ((int) ($e->errorInfo[1] ?? 0) !== 1062) {
                throw $e;
            }

            return Collaborator::query()
                ->where('email', '=', $email)
                ->where('role', CollaboratorRole::EXTERNAL)
                ->first();
        }
    }
}
