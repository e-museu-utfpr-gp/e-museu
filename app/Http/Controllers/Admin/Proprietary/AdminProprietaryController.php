<?php

namespace App\Http\Controllers\Admin\Proprietary;

use App\Http\Controllers\Admin\AdminBaseController;
use App\Http\Controllers\Admin\Concerns\LocksSubject;
use App\Http\Requests\Proprietary\NewProprietaryRequest;
use App\Http\Requests\Proprietary\ProprietaryRequest;
use App\Models\Proprietary\Proprietary;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class AdminProprietaryController extends AdminBaseController
{
    use LocksSubject;

    public function index(Request $request): View
    {
        $searchColumn = $request->search_column;
        $search = $request->search;
        $sort = $request->sort;
        $order = $request->order;
        $count = Proprietary::count();

        $query = Proprietary::query();

        if ($searchColumn && $search) {
            if ($search === 'sim') {
                $query->where($searchColumn, true);
            } elseif ($search === 'não' || $search === 'nao') {
                $query->where($searchColumn, false);
            } else {
                $query->where($searchColumn, 'LIKE', "%{$search}%");
            }
        }

        if ($sort && $order) {
            $query->orderBy($sort, $order);
        }

        $proprietaries = $query->paginate(10)->withQueryString();

        return view('admin.proprietary.proprietaries.index', compact('proprietaries', 'count'));
    }

    public function show(string $id): View
    {
        $proprietary = Proprietary::findOrFail($id);

        return view('admin.proprietary.proprietaries.show', compact('proprietary'));
    }

    public function create(): View
    {
        return view('admin.proprietary.proprietaries.create');
    }

    public function store(ProprietaryRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $rules = [
            'contact' => 'unique:proprietaries',
        ];

        $messages = [
            'contact.unique:proprietaries' => __('app.proprietary.contact_unique'),
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $proprietary = Proprietary::create($data);

        $message = 'Colaborador adicionado com sucesso.';

        return redirect()->route('admin.proprietaries.show', $proprietary)->with('success', $message);
    }

    public function edit(string $id): View
    {
        $proprietary = Proprietary::findOrFail($id);
        $this->requireUnlocked($proprietary);

        $this->lock($proprietary);

        return view('admin.proprietary.proprietaries.edit', compact('proprietary'));
    }

    public function update(NewProprietaryRequest $request, Proprietary $proprietary): RedirectResponse
    {
        $this->requireUnlocked($proprietary);

        $data = $request->validated();

        $rules = [
            'contact' => 'unique:proprietaries',
        ];

        $messages = [
            'contact.unique:proprietaries' => __('app.proprietary.contact_unique'),
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $proprietary->update($data);

        $this->unlock($proprietary);

        $message = 'Colaborador atualizado com sucesso.';

        return redirect()->route('admin.proprietaries.show', $proprietary)->with('success', $message);
    }

    public function destroy(Proprietary $proprietary): RedirectResponse
    {
        $this->requireUnlocked($proprietary);

        $this->unlock($proprietary);

        $proprietary->delete();

        return redirect()->route('admin.proprietaries.index')->with('success', 'Colaborador excluído com sucesso.');
    }
}
