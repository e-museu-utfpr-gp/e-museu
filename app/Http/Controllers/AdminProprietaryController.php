<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Http\Middleware\CheckLock;

use Illuminate\Http\Request;
use App\Http\Requests\ProprietaryRequest;
use App\Http\Requests\NewProprietaryRequest;

use App\Models\Proprietary;

class AdminProprietaryController extends Controller
{
    public function __construct()
    {
        $this->middleware(CheckLock::class)->only(['edit', 'update', 'destroy']);
    }

    public function index(Request $request)
    {
        $searchColumn = $request->search_column;
        $search = $request->search;
        $sort = $request->sort;
        $order = $request->order;
        $count = Proprietary::count();

        $query = Proprietary::query();

        if ($searchColumn && $search) {
            if ($search == 'sim')
                $query->where($searchColumn, true);
            elseif ($search == 'não' || $search == 'nao')
                $query->where($searchColumn, false);
            else
                $query->where($searchColumn, 'LIKE', "%{$search}%");
        }

        if ($sort && $order) {
            if ($order == 'asc')
                $query->orderBy($sort, 'desc');
            else
                $query->orderBy($sort, 'asc');
        }

        $proprietaries = $query->paginate(10)->withQueryString();

        return view('admin.proprietaries.index', compact('proprietaries', 'count'));
    }

    public function show($id)
    {
        $proprietary = Proprietary::find($id);

        return view('admin.proprietaries.show', compact('proprietary'));
    }

    public function create()
    {
        return view('admin.proprietaries.create');
    }

    public function store(ProprietaryRequest $request)
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

        return redirect()->route('admin.proprietaries.show', $proprietary)->with('success', 'Colaborador adicionado com sucesso.');
    }

    public function edit($id)
    {
        $proprietary = Proprietary::find($id);

        $this->lock($proprietary);

        return view('admin.proprietaries.edit', compact('proprietary'));
    }

    public function update(NewProprietaryRequest $request, Proprietary $proprietary)
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

        return redirect()->route('admin.proprietaries.show', $proprietary)->with('success', 'Colaborador atualizado com sucesso.');
    }

    public function destroy(Proprietary $proprietary)
    {
        $this->unlock($proprietary);

        $proprietary->delete();

        return redirect()->route('admin.proprietaries.index')->with('success', 'Colaborador excluído com sucesso.');
    }
}
