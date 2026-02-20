<?php

namespace App\Http\Controllers\Identity;

use App\Http\Controllers\Controller;
use App\Http\Requests\Identity\UserRequest;
use App\Models\Identity\Lock;
use App\Models\Identity\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::query();
        $count = User::count();

        if ($request->search_column && $request->search) {
            $query->where($request->search_column, 'LIKE', "%{$request->search}%");
        }

        if ($request->sort && $request->order) {
            $query->orderBy($request->sort, $request->order);
        }

        $users = $query->paginate(50)->withQueryString();

        return view('admin.users.index', compact('users', 'count'));
    }

    public function show(string $id): View
    {
        $user = User::findOrFail($id);

        return view('admin.users.show', compact('user'));
    }

    public function create(): View
    {
        return view('admin.users.create');
    }

    public function store(UserRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $data['password'] = Hash::make($request->password);

        $user = User::create($data);

        return redirect()->route('admin.users.show', $user)->with('success', 'Administrador adicionado com sucesso.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Administrador excluído com sucesso.');
    }

    public function destroyLock(string $id): RedirectResponse
    {
        $lock = Lock::where('user_id', $id)->first();

        if ($lock) {
            $lock->delete();
            $message = 'Tranca de edição relacionada ao administrador removida com sucesso.';

            return redirect()->route('admin.users.index')->with('success', $message);
        }

        return back()->withErrors(['Nenhuma tranca está associada a este administrador.']);
    }
}
