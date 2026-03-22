<?php

namespace App\Services\Collaborator;

use App\Enums\Collaborator\CollaboratorRole;
use App\Models\Collaborator\Collaborator;
use App\Support\Admin\AdminIndexConfig;
use App\Support\Admin\AdminIndexQueryBuilder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class CollaboratorService
{
    /**
     * @return array{collaborators: LengthAwarePaginator<int, Collaborator>, count: int}
     */
    public function getPaginatedCollaboratorsForAdminIndex(Request $request): array
    {
        $count = Collaborator::count();
        $query = Collaborator::query();

        AdminIndexQueryBuilder::build($query, $request, AdminIndexConfig::collaborators());

        $collaborators = $query->paginate(10)->withQueryString();

        return ['collaborators' => $collaborators, 'count' => $count];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function createCollaborator(array $data): Collaborator
    {
        return Collaborator::create($data);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updateCollaborator(Collaborator $collaborator, array $data): void
    {
        $collaborator->update($data);
    }

    public function deleteCollaborator(Collaborator $collaborator): void
    {
        $collaborator->delete();
    }

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
