<?php

namespace Tests\Feature\Middleware;

use App\Models\Identity\Admin;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Group;

/**
 * Covers {@see \App\Http\Middleware\Auth\RedirectIfAuthenticated} (alias {@code redirectIfAuthenticated}).
 */
#[Group('middleware')]
class RedirectIfAuthenticatedMiddlewareTest extends MysqlMiddlewareTestCase
{
    public function test_authenticated_admin_visiting_login_is_redirected_to_admin_items_index(): void
    {
        $admin = Admin::create([
            'username' => 'redirect_if_auth_' . uniqid('', false),
            'password' => Hash::make('secret'),
        ]);

        $this->actingAs($admin, 'web');

        $this->get(route('login'))
            ->assertRedirect(route('admin.catalog.items.index'));
    }
}
