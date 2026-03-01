<?php

namespace App\Http\Controllers\Admin\Identity;

use App\Http\Controllers\Admin\AdminBaseController;
use App\Http\Requests\Identity\UserRequest;
use App\Models\Identity\Lock;
use App\Models\Identity\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AdminUserController extends AdminBaseController
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

        return view('admin.identity.users.index', compact('users', 'count'));
    }

    public function show(string $id): View
    {
        $user = User::findOrFail($id);

        return view('admin.identity.users.show', compact('user'));
    }

    public function create(): View
    {
        return view('admin.identity.users.create');
    }

    public function store(UserRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $data['password'] = Hash::make($request->password);

        $user = User::create($data);

        return redirect()->route('admin.users.show', $user)->with('success', __('app.identity.user.created'));
    }

    public function destroy(User $user): RedirectResponse
    {
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', __('app.identity.user.deleted'));
    }

    public function destroyLock(string $id): RedirectResponse
    {
        $lock = Lock::where('user_id', $id)->first();

        if ($lock) {
            $lock->delete();

            return redirect()->route('admin.users.index')->with('success', __('app.identity.lock_removed'));
        }

        return back()->withErrors([__('app.identity.lock_not_found')]);
    }
}
