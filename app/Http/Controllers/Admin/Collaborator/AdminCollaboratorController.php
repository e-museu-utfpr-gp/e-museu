<?php

namespace App\Http\Controllers\Admin\Collaborator;

use App\Enums\CollaboratorRole;
use App\Http\Controllers\Admin\AdminBaseController;
use App\Http\Controllers\Admin\Concerns\LocksSubject;
use App\Http\Requests\Collaborator\StoreCollaboratorRequest;
use App\Http\Requests\Collaborator\UpdateCollaboratorRequest;
use App\Models\Collaborator\Collaborator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminCollaboratorController extends AdminBaseController
{
    use LocksSubject;

    public function index(Request $request): View
    {
        $count = Collaborator::count();
        $query = Collaborator::query();

        $this->applySearchFilter($query, $request->search_column, $request->search);

        if ($request->sort && $request->order) {
            $query->orderBy($request->sort, $request->order);
        }

        $collaborators = $query->paginate(10)->withQueryString();

        return view('admin.collaborators.index', compact('collaborators', 'count'));
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<Collaborator>  $query
     */
    private function applySearchFilter($query, ?string $searchColumn, ?string $search): void
    {
        if (! $searchColumn || ! $search) {
            return;
        }

        if (
            $searchColumn === 'role' && in_array(
                strtolower($search),
                [
                    CollaboratorRole::INTERNAL->value,
                    CollaboratorRole::EXTERNAL->value,
                ],
                true
            )
        ) {
            $query->where($searchColumn, strtolower($search));

            return;
        }

        if ($search === 'sim') {
            $query->where($searchColumn, true);

            return;
        }

        if ($search === 'não' || $search === 'nao') {
            $query->where($searchColumn, false);

            return;
        }

        $query->where($searchColumn, 'LIKE', "%{$search}%");
    }

    public function show(string $id): View
    {
        $collaborator = Collaborator::findOrFail($id);

        return view('admin.collaborators.show', compact('collaborator'));
    }

    public function create(): View
    {
        return view('admin.collaborators.create');
    }

    public function store(StoreCollaboratorRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['blocked'] = $request->boolean('blocked', false);

        $collaborator = Collaborator::create($data);

        $message = 'Colaborador adicionado com sucesso.';

        return redirect()->route('admin.collaborators.show', $collaborator)->with('success', $message);
    }

    public function edit(string $id): View
    {
        $collaborator = Collaborator::findOrFail($id);
        $this->requireUnlocked($collaborator);

        $this->lock($collaborator);

        return view('admin.collaborators.edit', compact('collaborator'));
    }

    public function update(UpdateCollaboratorRequest $request, Collaborator $collaborator): RedirectResponse
    {
        $this->requireUnlocked($collaborator);

        $data = $request->validated();

        $collaborator->update($data);

        $this->unlock($collaborator);

        $message = 'Colaborador atualizado com sucesso.';

        return redirect()->route('admin.collaborators.show', $collaborator)->with('success', $message);
    }

    public function destroy(Collaborator $collaborator): RedirectResponse
    {
        $this->requireUnlocked($collaborator);

        $this->unlock($collaborator);

        $collaborator->delete();

        return redirect()->route('admin.collaborators.index')->with('success', 'Colaborador excluído com sucesso.');
    }
}
