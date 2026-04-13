<?php

namespace Tests\Feature\Admin;

use App\Http\Middleware\VerifyAntiBotChallenge;
use App\Models\Identity\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('mysql')]
class AdminLoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        if (! extension_loaded('pdo_mysql')) {
            $this->markTestSkipped('pdo_mysql required');
        }
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
}
