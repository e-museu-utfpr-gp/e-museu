<?php

declare(strict_types=1);

namespace Tests\Feature\Admin\Identity;

use App\Models\Collaborator\Collaborator;
use App\Models\Identity\Admin;
use App\Models\Identity\Lock;
use Database\Factories\Collaborator\CollaboratorFactory;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Group;
use Tests\Support\AbstractMysqlRefreshDatabaseTestCase;

#[Group('mysql')]
class AdminControllerTest extends AbstractMysqlRefreshDatabaseTestCase
{
    public function test_guest_is_redirected_from_admins_index_to_login(): void
    {
        $this->get(route('admin.identity.admins.index'))
            ->assertRedirect(route('login'));
    }

    public function test_guest_is_redirected_from_admins_create_to_login(): void
    {
        $this->get(route('admin.identity.admins.create'))
            ->assertRedirect(route('login'));
    }

    public function test_guest_is_redirected_from_admins_show_to_login(): void
    {
        $admin = $this->createAdmin();

        $this->get(route('admin.identity.admins.show', $admin))
            ->assertRedirect(route('login'));
    }

    public function test_guest_cannot_store_admin(): void
    {
        $this->get(route('login'));

        $this->post(route('admin.identity.admins.store'), [
            '_token' => session()->token(),
        ])->assertRedirect(route('login'));
    }

    public function test_guest_cannot_destroy_admin(): void
    {
        $admin = $this->createAdmin();
        $this->get(route('login'));

        $this->delete(route('admin.identity.admins.destroy', $admin), [
            '_token' => session()->token(),
        ])->assertRedirect(route('login'));
    }

    public function test_guest_cannot_delete_lock(): void
    {
        $admin = $this->createAdmin();
        $this->get(route('login'));

        $this->delete(route('admin.identity.admins.delete-lock', ['id' => $admin->id]), [
            '_token' => session()->token(),
        ])->assertRedirect(route('login'));
    }

    public function test_admin_can_view_admins_index(): void
    {
        $this->actingAs($this->createAdmin());

        $this->get(route('admin.identity.admins.index'))
            ->assertOk()
            ->assertViewIs('pages.admin.identity.admins.index')
            ->assertViewHas([
                'admins',
                'count',
                'searchOptions',
                'sortColumns',
                'searchBooleanColumns',
            ]);
    }

    public function test_admin_index_lists_username(): void
    {
        $subject = $this->createAdmin();

        $this->actingAs($this->createAdmin());

        $this->get(route('admin.identity.admins.index'))
            ->assertOk()
            ->assertSee($subject->username, false);
    }

    public function test_admin_can_view_create_form(): void
    {
        $this->actingAs($this->createAdmin());

        $this->get(route('admin.identity.admins.create'))
            ->assertOk()
            ->assertViewIs('pages.admin.identity.admins.create');
    }

    public function test_admin_can_view_show(): void
    {
        $subject = $this->createAdmin();

        $this->actingAs($this->createAdmin());

        $this->get(route('admin.identity.admins.show', $subject))
            ->assertOk()
            ->assertViewIs('pages.admin.identity.admins.show')
            ->assertViewHas('admin');
    }

    public function test_admin_can_store_admin_and_redirect_to_show(): void
    {
        $username = 'new_admin_' . uniqid('', false);

        $this->actingAs($this->createAdmin());
        $this->get(route('admin.identity.admins.create'));

        $response = $this->post(route('admin.identity.admins.store'), [
            '_token' => session()->token(),
            'username' => $username,
            'password' => 'plain-secret-1',
        ]);

        $created = Admin::query()->where('username', $username)->first();
        $this->assertNotNull($created);

        $response->assertRedirect(route('admin.identity.admins.show', $created));
        $response->assertSessionHas('success');
        $this->assertTrue(Hash::check('plain-secret-1', $created->password));
    }

    public function test_store_redirects_with_errors_when_payload_invalid(): void
    {
        $this->actingAs($this->createAdmin());
        $this->from(route('admin.identity.admins.create'));

        $this->post(route('admin.identity.admins.store'), [
            '_token' => session()->token(),
        ])
            ->assertRedirect(route('admin.identity.admins.create'))
            ->assertSessionHasErrors(['username', 'password']);
    }

    public function test_store_rejects_duplicate_username(): void
    {
        $existing = $this->createAdmin();

        $this->actingAs($this->createAdmin());
        $this->from(route('admin.identity.admins.create'));

        $this->post(route('admin.identity.admins.store'), [
            '_token' => session()->token(),
            'username' => $existing->username,
            'password' => 'another-secret',
        ])
            ->assertRedirect(route('admin.identity.admins.create'))
            ->assertSessionHasErrors('username');
    }

    public function test_admin_can_destroy_other_admin(): void
    {
        $actor = $this->createAdmin();
        $target = $this->createAdmin();

        $this->actingAs($actor);
        $this->get(route('admin.identity.admins.index'));

        $response = $this->delete(route('admin.identity.admins.destroy', $target), [
            '_token' => session()->token(),
        ]);

        $response->assertRedirect(route('admin.identity.admins.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('admins', ['id' => $target->id]);
        $this->assertDatabaseHas('admins', ['id' => $actor->id]);
    }

    public function test_show_returns_not_found_for_unknown_admin_id(): void
    {
        $this->actingAs($this->createAdmin());

        $this->get(route('admin.identity.admins.show', ['admin' => 9_999_999]))
            ->assertNotFound();
    }

    public function test_delete_lock_removes_lock_and_redirects_to_index(): void
    {
        $actor = $this->createAdmin();
        $lockHolder = $this->createAdmin();
        $collaborator = CollaboratorFactory::new()->create();

        Lock::query()->create([
            'lockable_id' => $collaborator->id,
            'lockable_type' => Collaborator::class,
            'admin_id' => $lockHolder->id,
            'expiry_date' => now()->addHour(),
        ]);

        $this->actingAs($actor);
        $this->from(route('admin.identity.admins.index'));

        $response = $this->delete(route('admin.identity.admins.delete-lock', ['id' => $lockHolder->id]), [
            '_token' => session()->token(),
        ]);

        $response->assertRedirect(route('admin.identity.admins.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('locks', ['admin_id' => $lockHolder->id]);
    }

    public function test_delete_lock_redirects_back_with_errors_when_no_lock(): void
    {
        $actor = $this->createAdmin();
        $other = $this->createAdmin();

        $this->actingAs($actor);
        $this->from(route('admin.identity.admins.index'));

        $response = $this->delete(route('admin.identity.admins.delete-lock', ['id' => $other->id]), [
            '_token' => session()->token(),
        ]);

        $response->assertRedirect(route('admin.identity.admins.index'));
        $response->assertSessionHas('errors');
    }

    private function createAdmin(): Admin
    {
        return Admin::create([
            'username' => 'identity_admin_' . uniqid('', false),
            'password' => Hash::make('secret'),
        ]);
    }
}
