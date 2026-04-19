<?php

namespace Tests\Feature\Admin\Collaborator;

use App\Enums\Collaborator\CollaboratorRole;
use App\Models\Collaborator\Collaborator;
use App\Models\Identity\Admin;
use Database\Factories\Collaborator\CollaboratorFactory;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Group;
use Tests\Support\AbstractMysqlRefreshDatabaseTestCase;

#[Group('mysql')]
class AdminCollaboratorControllerTest extends AbstractMysqlRefreshDatabaseTestCase
{
    public function test_guest_is_redirected_from_collaborators_index_to_login(): void
    {
        $this->get(route('admin.collaborators.index'))
            ->assertRedirect(route('login'));
    }

    public function test_guest_is_redirected_from_collaborators_create_to_login(): void
    {
        $this->get(route('admin.collaborators.create'))
            ->assertRedirect(route('login'));
    }

    public function test_guest_is_redirected_from_collaborators_show_to_login(): void
    {
        $collaborator = CollaboratorFactory::new()->create();

        $this->get(route('admin.collaborators.show', $collaborator))
            ->assertRedirect(route('login'));
    }

    public function test_guest_is_redirected_from_collaborators_edit_to_login(): void
    {
        $collaborator = CollaboratorFactory::new()->create();

        $this->get(route('admin.collaborators.edit', $collaborator))
            ->assertRedirect(route('login'));
    }

    public function test_guest_cannot_store_collaborator(): void
    {
        $this->get(route('login'));

        $this->post(route('admin.collaborators.store'), [
            '_token' => session()->token(),
        ])->assertRedirect(route('login'));
    }

    public function test_guest_cannot_update_collaborator(): void
    {
        $collaborator = CollaboratorFactory::new()->create();
        $this->get(route('login'));

        $this->put(route('admin.collaborators.update', $collaborator), [
            '_token' => session()->token(),
        ])->assertRedirect(route('login'));
    }

    public function test_guest_cannot_destroy_collaborator(): void
    {
        $collaborator = CollaboratorFactory::new()->create();
        $this->get(route('login'));

        $this->delete(route('admin.collaborators.destroy', $collaborator), [
            '_token' => session()->token(),
        ])->assertRedirect(route('login'));
    }

    public function test_admin_can_view_collaborators_index(): void
    {
        $this->actingAs($this->createAdmin());

        $this->get(route('admin.collaborators.index'))
            ->assertOk()
            ->assertViewIs('pages.admin.collaborators.index')
            ->assertViewHas([
                'collaborators',
                'count',
                'searchOptions',
                'sortColumns',
                'searchBooleanColumns',
            ]);
    }

    public function test_admin_index_lists_collaborator_email(): void
    {
        $collaborator = CollaboratorFactory::new()->create([
            'email' => 'admin-collab-index-' . uniqid('', false) . '@example.com',
        ]);

        $this->actingAs($this->createAdmin());

        $this->get(route('admin.collaborators.index'))
            ->assertOk()
            ->assertSee($collaborator->email, false);
    }

    public function test_admin_can_view_collaborators_create_form(): void
    {
        $this->actingAs($this->createAdmin());

        $this->get(route('admin.collaborators.create'))
            ->assertOk()
            ->assertViewIs('pages.admin.collaborators.create');
    }

    public function test_admin_can_view_collaborator_show(): void
    {
        $collaborator = CollaboratorFactory::new()->create();

        $this->actingAs($this->createAdmin());

        $this->get(route('admin.collaborators.show', $collaborator))
            ->assertOk()
            ->assertViewIs('pages.admin.collaborators.show')
            ->assertViewHas('collaborator');
    }

    public function test_admin_can_store_collaborator_and_redirect_to_show(): void
    {
        $email = 'admin-collab-store-' . uniqid('', false) . '@example.com';

        $this->actingAs($this->createAdmin());
        $this->get(route('admin.collaborators.create'));

        $response = $this->post(route('admin.collaborators.store'), [
            '_token' => session()->token(),
            'full_name' => 'Stored Collaborator',
            'email' => $email,
            'role' => CollaboratorRole::EXTERNAL->value,
            'blocked' => 0,
        ]);

        $collaborator = Collaborator::query()->where('email', $email)->first();
        $this->assertNotNull($collaborator);

        $response->assertRedirect(route('admin.collaborators.show', $collaborator));
        $response->assertSessionHas('success');
        $this->assertSame('Stored Collaborator', $collaborator->full_name);
        $this->assertFalse($collaborator->blocked);
    }

    public function test_store_redirects_with_errors_when_payload_invalid(): void
    {
        $this->actingAs($this->createAdmin());
        $this->from(route('admin.collaborators.create'));

        $this->post(route('admin.collaborators.store'), [
            '_token' => session()->token(),
        ])
            ->assertRedirect(route('admin.collaborators.create'))
            ->assertSessionHasErrors(['full_name', 'email', 'role']);
    }

    public function test_store_rejects_duplicate_email(): void
    {
        $existing = CollaboratorFactory::new()->create([
            'email' => 'dup-collab-' . uniqid('', false) . '@example.com',
        ]);

        $this->actingAs($this->createAdmin());
        $this->from(route('admin.collaborators.create'));

        $this->post(route('admin.collaborators.store'), [
            '_token' => session()->token(),
            'full_name' => 'Other',
            'email' => $existing->email,
            'role' => CollaboratorRole::EXTERNAL->value,
            'blocked' => 0,
        ])
            ->assertRedirect(route('admin.collaborators.create'))
            ->assertSessionHasErrors('email');
    }

    public function test_admin_can_open_edit_form(): void
    {
        $collaborator = CollaboratorFactory::new()->create();

        $this->actingAs($this->createAdmin());

        $this->get(route('admin.collaborators.edit', $collaborator))
            ->assertOk()
            ->assertViewIs('pages.admin.collaborators.edit')
            ->assertViewHas('collaborator');
    }

    public function test_admin_can_update_collaborator_and_redirect_to_show(): void
    {
        $collaborator = CollaboratorFactory::new()->create([
            'full_name' => 'Before Update',
            'email' => 'before-update-' . uniqid('', false) . '@example.com',
            'role' => CollaboratorRole::EXTERNAL,
        ]);

        $this->actingAs($this->createAdmin());
        $this->get(route('admin.collaborators.edit', $collaborator));

        $response = $this->put(route('admin.collaborators.update', $collaborator), [
            '_token' => session()->token(),
            'full_name' => 'After Update',
            'email' => $collaborator->email,
            'role' => CollaboratorRole::EXTERNAL->value,
            'blocked' => 0,
        ]);

        $response->assertRedirect(route('admin.collaborators.show', $collaborator));
        $response->assertSessionHas('success');

        $collaborator->refresh();
        $this->assertSame('After Update', $collaborator->full_name);
    }

    public function test_admin_can_destroy_collaborator(): void
    {
        $collaborator = CollaboratorFactory::new()->create();

        $this->actingAs($this->createAdmin());
        $this->get(route('admin.collaborators.index'));

        $response = $this->delete(route('admin.collaborators.destroy', $collaborator), [
            '_token' => session()->token(),
        ]);

        $response->assertRedirect(route('admin.collaborators.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('collaborators', ['id' => $collaborator->id]);
    }

    public function test_show_returns_not_found_for_unknown_collaborator_id(): void
    {
        $this->actingAs($this->createAdmin());

        $this->get(route('admin.collaborators.show', ['collaborator' => 9_999_999]))
            ->assertNotFound();
    }

    private function createAdmin(): Admin
    {
        return Admin::create([
            'username' => 'collab_ctrl_' . uniqid('', false),
            'password' => Hash::make('secret'),
        ]);
    }
}
