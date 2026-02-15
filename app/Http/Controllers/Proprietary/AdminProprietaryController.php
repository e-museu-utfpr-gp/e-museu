<?php

namespace App\Http\Controllers\Proprietary;

use App\Http\Controllers\AdminBaseController;
use App\Http\Middleware\CheckLock;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Http\Requests\Proprietary\NewProprietaryRequest;
use App\Http\Requests\Proprietary\ProprietaryRequest;
use App\Models\Proprietary\Proprietary;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AdminProprietaryController extends AdminBaseController
{
    public function __construct()
    {
        $this->middleware(CheckLock::class)->only(['edit', 'update', 'destroy']);
    }

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
            if ($order === 'asc') {
                $query->orderBy($sort, 'desc');
            } else {
                $query->orderBy($sort, 'asc');
            }
        }

        $proprietaries = $query->paginate(10)->withQueryString();

        return view('admin.proprietaries.index', compact('proprietaries', 'count'));
    }

    public function show(string $id): View
    {
        $proprietary = Proprietary::find($id);

        return view('admin.proprietaries.show', compact('proprietary'));
    }

    public function create(): View
    {
        return view('admin.proprietaries.create');
    }

    public function store(ProprietaryRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $rules = [
            'contact' => 'unique:proprietaries',
        ];

        $messages = [
            'contact.unique:proprietaries' => 'O campo Email já está sendo utilizado',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return back()->withErrors($messages)->withInput();
        }

        $proprietary = Proprietary::create($data);

        $message = 'Colaborador adicionado com sucesso.';

        return redirect()->route('admin.proprietaries.show', $proprietary)->with('success', $message);
    }

    public function edit(string $id): View
    {
        $proprietary = Proprietary::findOrFail($id);

        $this->lock($proprietary);

        return view('admin.proprietaries.edit', compact('proprietary'));
    }

    public function update(NewProprietaryRequest $request, Proprietary $proprietary): RedirectResponse
    {
        $data = $request->validated();

        $rules = [
            'contact' => 'unique:proprietaries',
        ];

        $messages = [
            'contact.unique:proprietaries' => 'O campo Email já está sendo utilizado',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return back()->withErrors($messages)->withInput();
        }

        $proprietary->update($data);

        $this->unlock($proprietary);

        $message = 'Colaborador atualizado com sucesso.';

        return redirect()->route('admin.proprietaries.show', $proprietary)->with('success', $message);
    }

    public function destroy(Proprietary $proprietary): RedirectResponse
    {
        $this->unlock($proprietary);

        $proprietary->delete();

        return redirect()->route('admin.proprietaries.index')->with('success', 'Colaborador excluído com sucesso.');
    }
}
