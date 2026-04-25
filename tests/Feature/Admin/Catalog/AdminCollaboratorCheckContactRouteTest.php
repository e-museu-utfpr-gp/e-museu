<?php

declare(strict_types=1);

namespace Tests\Feature\Admin\Catalog;

use App\Models\Identity\Admin;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Group;
use Tests\Support\AbstractMysqlRefreshDatabaseTestCase;

#[Group('mysql')]
class AdminCollaboratorCheckContactRouteTest extends AbstractMysqlRefreshDatabaseTestCase
{
    public function test_guest_cannot_post_admin_catalog_collaborators_check_contact(): void
    {
        $this->postJson(route('admin.catalog.collaborators.check-contact'), [
            'email' => 'someone@example.com',
        ])->assertUnauthorized();
    }

    public function test_authenticated_admin_can_post_admin_catalog_collaborators_check_contact(): void
    {
        $admin = Admin::create([
            'username' => 'check_contact_' . uniqid('', false),
            'password' => Hash::make('secret'),
        ]);

        $this->actingAs($admin)->postJson(route('admin.catalog.collaborators.check-contact'), [
            'email' => '',
        ])
            ->assertOk()
            ->assertJson([
                'skip_contact_check' => true,
                'email_verified' => null,
            ]);
    }
}
