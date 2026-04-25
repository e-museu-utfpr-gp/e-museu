<?php

declare(strict_types=1);

namespace App\Actions\Catalog;

use App\Enums\Collaborator\CollaboratorRole;
use App\Models\Collaborator\Collaborator;
use Illuminate\Database\QueryException;

/**
 * Resolves the collaborator row for a public single-extra submission when e-mail verification is disabled.
 *
 * Prefer {@code collaborator_id} when present and valid; otherwise find by e-mail or create an external collaborator.
 */
final class ResolveSingleExtraCollaboratorAction
{
    /**
     * @param  array<string, mixed>  $collaboratorData
     */
    public function handle(array $collaboratorData, int $submittedCollaboratorId): ?Collaborator
    {
        if ($submittedCollaboratorId > 0) {
            $resolved = Collaborator::query()->find($submittedCollaboratorId);
            if ($resolved !== null) {
                return $resolved;
            }
        }

        $email = trim((string) ($collaboratorData['email'] ?? ''));
        $fullName = trim((string) ($collaboratorData['full_name'] ?? ''));
        if ($email === '' || $fullName === '') {
            return null;
        }

        $existing = Collaborator::query()->where('email', '=', $email)->first();
        if ($existing !== null) {
            return $existing;
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

            return Collaborator::query()->where('email', '=', $email)->first();
        }
    }
}
