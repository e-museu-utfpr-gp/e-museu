<?php

namespace App\Services\Identity;

use App\Models\Identity\Admin;
use App\Models\Identity\Lock;
use App\Support\Admin\AdminIndexConfig;
use App\Support\Admin\AdminIndexQueryBuilder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminService
{
    /**
     * @return array{admins: LengthAwarePaginator<int, Admin>, count: int}
     */
    public function getPaginatedAdminsForAdminIndex(Request $request): array
    {
        $query = Admin::query()->with('locks');

        AdminIndexQueryBuilder::build($query, $request, AdminIndexConfig::admins());

        $admins = $query->paginate(50)->withQueryString();

        return [
            'admins' => $admins,
            'count' => $admins->total(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data  Must include 'username' and 'password' (plain).
     *                                     Password is hashed before create.
     */
    public function createAdmin(array $data): Admin
    {
        $data['password'] = Hash::make($data['password']);

        return Admin::create($data);
    }

    public function deleteAdmin(Admin $admin): void
    {
        $admin->delete();
    }

    /**
     * Remove lock by admin id. Returns true if a lock was found and deleted.
     */
    public function removeLockByAdminId(string $adminId): bool
    {
        $lock = Lock::where('admin_id', $adminId)->first();

        if ($lock === null) {
            return false;
        }

        $lock->delete();

        return true;
    }
}
