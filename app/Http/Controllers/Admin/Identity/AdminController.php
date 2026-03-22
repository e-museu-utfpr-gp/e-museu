<?php

namespace App\Http\Controllers\Admin\Identity;

use App\Http\Controllers\Admin\AdminBaseController;
use App\Http\Requests\Admin\Identity\AdminRequest;
use App\Models\Identity\Admin;
use App\Services\Identity\AdminService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminController extends AdminBaseController
{
    public function index(Request $request, AdminService $adminService): View
    {
        $result = $adminService->getPaginatedAdminsForAdminIndex($request);

        return view('pages.admin.identity.admins.index', [
            'admins' => $result['admins'],
            'count' => $result['count'],
        ]);
    }

    public function show(Admin $admin): View
    {
        return view('pages.admin.identity.admins.show', compact('admin'));
    }

    public function create(): View
    {
        return view('pages.admin.identity.admins.create');
    }

    public function store(AdminRequest $request, AdminService $adminService): RedirectResponse
    {
        $admin = $adminService->createAdmin($request->validated());

        return redirect()->route('admin.identity.admins.show', $admin)->with('success', __('app.identity.admin.created'));
    }

    public function destroy(Admin $admin, AdminService $adminService): RedirectResponse
    {
        $adminService->deleteAdmin($admin);

        return redirect()->route('admin.identity.admins.index')->with('success', __('app.identity.admin.deleted'));
    }

    public function destroyLock(string $id, AdminService $adminService): RedirectResponse
    {
        if ($adminService->removeLockByAdminId($id)) {
            return redirect()->route('admin.identity.admins.index')->with('success', __('app.identity.lock_removed'));
        }

        return back()->withErrors([__('app.identity.lock_not_found')]);
    }
}
