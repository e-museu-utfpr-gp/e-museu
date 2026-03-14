<?php

namespace App\Services\Collaborator;

use App\Enums\Collaborator\CollaboratorRole;
use App\Models\Collaborator\Collaborator;
use Illuminate\Database\Eloquent\Collection;

class CollaboratorService
{
    /**
     * @param  array<string, mixed>  $collaboratorData
     */
    public function resolveOrCreateCollaborator(array $collaboratorData): Collaborator
    {
        $collaborator = Collaborator::where('contact', '=', $collaboratorData['contact'])->first();

        return $collaborator ?? $this->storeCollaborator($collaboratorData);
    }

    /**
     * @param  array<string, mixed>  $collaboratorData
     */
    public function storeCollaborator(array $collaboratorData): Collaborator
    {
        $collaboratorData['role'] = CollaboratorRole::EXTERNAL;

        return Collaborator::create($collaboratorData);
    }

    /**
     * @return Collection<int, Collaborator>
     */
    public function getForForm(): Collection
    {
        return Collaborator::orderBy('full_name')->get();
    }

    public function findExternalByContact(string $contact): ?Collaborator
    {
        return Collaborator::where('contact', 'LIKE', $contact)
            ->where('role', CollaboratorRole::EXTERNAL)
            ->where('blocked', false)
            ->first();
    }
}
