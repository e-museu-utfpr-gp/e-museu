<?php

namespace App\Services\Collaborator;

use App\Enums\Collaborator\CollaboratorRole;
use App\Models\Collaborator\Collaborator;
use Illuminate\Database\Eloquent\Collection;

class CollaboratorService
{
    /**
     * Find collaborator by contact or create a new external collaborator.
     *
     * @param  array<string, mixed>  $collaboratorData
     */
    public function resolveOrCreateCollaborator(array $collaboratorData): Collaborator
    {
        $collaborator = Collaborator::where('contact', '=', $collaboratorData['contact'])->first();

        return $collaborator ?? $this->storeCollaborator($collaboratorData);
    }

    /**
     * Create a new external collaborator.
     *
     * @param  array<string, mixed>  $collaboratorData
     */
    public function storeCollaborator(array $collaboratorData): Collaborator
    {
        $collaboratorData['role'] = CollaboratorRole::EXTERNAL;

        return Collaborator::create($collaboratorData);
    }

    /**
     * All collaborators ordered by name (e.g. for admin form dropdowns).
     *
     * @return Collection<int, Collaborator>
     */
    public function getForForm(): Collection
    {
        return Collaborator::orderBy('full_name')->get();
    }
}
