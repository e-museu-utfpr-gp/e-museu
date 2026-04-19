<?php

namespace Tests\Feature\Admin\Auth;

use App\Http\Middleware\VerifyAntiBotChallenge;
use App\Models\Identity\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('mysql')]
class AdminLoginControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        if (! extension_loaded('pdo_mysql')) {
            $this->markTestSkipped('pdo_mysql required');
        }

        parent::setUp();
    }

    public function test_show_login_form_returns_login_view(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertViewIs('pages.admin.auth.login');
    }

    public function test_guest_post_logout_is_redirected_to_login(): void
    {
        $this->get(route('login'));

        $this->post(route('logout'), [
            '_token' => session()->token(),
        ])->assertRedirect(route('login'));
    }

    public function test_guest_get_admin_dashboard_redirects_to_login(): void
    {
        $this->get(url('/admin'))
            ->assertRedirect(route('login'));
    }

    public function test_admin_can_login_with_valid_credentials_without_turnstile_middleware(): void
    {
        $this->withoutMiddleware(VerifyAntiBotChallenge::class);

        $password = 'correct-password';
        $admin = Admin::create([
            'username' => 'login_ok_' . uniqid('', false),
            'password' => Hash::make($password),
        ]);

        $this->get(route('login'));
        $this->post(route('login'), [
            '_token' => session()->token(),
            'username' => $admin->username,
            'password' => $password,
        ])->assertRedirect('/admin/catalog/items');

        $this->assertAuthenticatedAs($admin);
    }

    public function test_admin_login_rejects_invalid_credentials(): void
    {
        $this->withoutMiddleware(VerifyAntiBotChallenge::class);

        $admin = Admin::create([
            'username' => 'bad_pw_' . uniqid('', false),
            'password' => Hash::make('real-password'),
        ]);

        $this->get(route('login'));
        $response = $this->from(route('login'))->post(route('login'), [
            '_token' => session()->token(),
            'username' => $admin->username,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('username');
    }

    public function test_authenticated_admin_visiting_login_is_redirected_to_catalog(): void
    {
        $admin = Admin::create([
            'username' => 'already_in_' . uniqid('', false),
            'password' => Hash::make('irrelevant'),
        ]);

        $this->actingAs($admin);

        $this->get(route('login'))
            ->assertRedirect(route('admin.catalog.items.index'));
    }

    public function test_admin_can_logout(): void
    {
        $this->withoutMiddleware(VerifyAntiBotChallenge::class);

        $password = 'logout-secret';
        $admin = Admin::create([
            'username' => 'logout_' . uniqid('', false),
            'password' => Hash::make($password),
        ]);

        $this->get(route('login'));
        $this->post(route('login'), [
            '_token' => session()->token(),
            'username' => $admin->username,
            'password' => $password,
        ]);

        $this->assertAuthenticatedAs($admin);

        $this->post(route('logout'), [
            '_token' => session()->token(),
        ]);

        $this->assertGuest();
    }
}
