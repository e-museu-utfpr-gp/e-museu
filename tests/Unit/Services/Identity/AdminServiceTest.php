<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Identity;

use App\Models\Catalog\Item;
use App\Models\Collaborator\Collaborator;
use App\Models\Identity\Admin;
use App\Models\Identity\Lock;
use App\Models\Location;
use App\Services\Identity\AdminService;
use Database\Factories\Catalog\ItemCategoryFactory;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Group;
use Tests\Unit\Services\ServiceMysqlTestCase;

#[Group('services')]
class AdminServiceTest extends ServiceMysqlTestCase
{
    public function test_create_admin_hashes_password(): void
    {
        $svc = app(AdminService::class);
        $admin = $svc->createAdmin([
            'username' => 'svc_admin_' . uniqid('', false),
            'password' => 'plain-secret',
        ]);

        $this->assertTrue(Hash::check('plain-secret', $admin->password));
    }

    public function test_remove_lock_by_admin_id_returns_false_when_missing(): void
    {
        $svc = app(AdminService::class);
        $this->assertFalse($svc->removeLockByAdminId('999999'));
    }

    public function test_remove_lock_by_admin_id_deletes_lock_row(): void
    {
        $admin = Admin::create([
            'username' => 'lock_owner_' . uniqid('', false),
            'password' => Hash::make('x'),
        ]);

        $categoryId = ItemCategoryFactory::new()->create()->id;
        $locationId = Location::factory()->create()->id;
        $collaboratorId = Collaborator::factory()->create()->id;
        $item = Item::factory()->create([
            'category_id' => $categoryId,
            'location_id' => $locationId,
            'collaborator_id' => $collaboratorId,
        ]);

        Lock::query()->create([
            'admin_id' => $admin->id,
            'lockable_type' => Item::class,
            'lockable_id' => $item->id,
            'expiry_date' => now()->addHour(),
        ]);

        $svc = app(AdminService::class);
        $this->assertTrue($svc->removeLockByAdminId((string) $admin->id));
        $this->assertSame(0, Lock::query()->where('admin_id', (string) $admin->id)->count());
    }
}
