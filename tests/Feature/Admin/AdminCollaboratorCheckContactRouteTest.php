<?php

namespace Tests\Feature\Admin;

use App\Models\Identity\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('mysql')]
class AdminCollaboratorCheckContactRouteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        if (! extension_loaded('pdo_mysql')) {
            $this->markTestSkipped('pdo_mysql required');
        }
    }

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
