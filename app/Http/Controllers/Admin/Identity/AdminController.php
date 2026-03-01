<?php

namespace App\Http\Controllers\Admin\Identity;

use App\Http\Controllers\Admin\AdminBaseController;
use App\Http\Requests\Identity\AdminRequest;
use App\Models\Identity\Lock;
use App\Models\Identity\Admin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AdminController extends AdminBaseController
{
    public function index(Request $request): View
    {
        $query = Admin::query();
        $count = Admin::count();

        if ($request->search_column && $request->search) {
            $query->where($request->search_column, 'LIKE', "%{$request->search}%");
        }

        if ($request->sort && $request->order) {
            $query->orderBy($request->sort, $request->order);
        }

        $admins = $query->paginate(50)->withQueryString();

        return view('admin.identity.admins.index', compact('admins', 'count'));
    }

    public function show(string $id): View
    {
        $admin = Admin::findOrFail($id);

        return view('admin.identity.admins.show', compact('admin'));
    }

    public function create(): View
    {
        return view('admin.identity.admins.create');
    }

    public function store(AdminRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $data['password'] = Hash::make($request->password);

        $admin = Admin::create($data);

        return redirect()->route('admin.admins.show', $admin)->with('success', __('app.identity.admin.created'));
    }

    public function destroy(Admin $admin): RedirectResponse
    {
        $admin->delete();

        return redirect()->route('admin.admins.index')->with('success', __('app.identity.admin.deleted'));
    }

    public function destroyLock(string $id): RedirectResponse
    {
        $lock = Lock::where('admin_id', $id)->first();

        if ($lock) {
            $lock->delete();

            return redirect()->route('admin.admins.index')->with('success', __('app.identity.lock_removed'));
        }

        return back()->withErrors([__('app.identity.lock_not_found')]);
    }
}
