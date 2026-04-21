<?php

declare(strict_types=1);

namespace App\Actions\Catalog\StoreItemContribution\Concerns;

use App\Models\Collaborator\Collaborator;
use LogicException;

trait ResolvesCollaboratorForContribution
{
    /**
     * @param  array<string, mixed>  $collaboratorData
     * @return Collaborator|array{status: 'internal_blocked'|'collaborator_blocked'|'email_unverified'}
     */
    private function collaboratorForContributionOrEarlyStatus(array $collaboratorData): Collaborator|array
    {
        $resolution = $this->contributionCollaborator->resolveForContribution($collaboratorData);
        if ($resolution['status'] !== 'ok') {
            return ['status' => $resolution['status']];
        }
        $collaborator = $resolution['collaborator'];
        if (! $collaborator instanceof Collaborator) {
            throw new LogicException(
                'resolveForContribution returned status ok without a Collaborator instance.',
            );
        }

        return $collaborator;
    }
}
