<?php

namespace Tests\Feature\AntiBot;

use App\Services\Collaborator\CatalogCollaboratorVerificationService;
use App\Support\Security\AntiBotVerifier;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AntiBotChallengeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'antibot.driver' => 'null',
            'antibot.turnstile.site_key' => '',
            'antibot.turnstile.secret_key' => '',
        ]);
        $this->app->forgetInstance(AntiBotVerifier::class);
    }

    public function test_request_verification_code_without_turnstile_token_when_antibot_disabled(): void
    {
        $this->mock(CatalogCollaboratorVerificationService::class, function ($mock): void {
            $mock->shouldReceive('requestEmailVerificationCode')
                ->once()
                ->andReturn(['status' => 'sent']);
        });

        $email = 'antibot-off-' . uniqid('', false) . '@example.com';

        $this->postJson(route('catalog.collaborators.request-verification-code'), [
            'email' => $email,
            'full_name' => 'Test User',
        ])
            ->assertOk()
            ->assertJsonFragment(['message' => __('app.collaborator.verify_code_sent')]);
    }

    public function test_request_verification_code_rejects_missing_token_when_turnstile_active(): void
    {
        $this->enableTurnstileForTests();

        $this->mock(CatalogCollaboratorVerificationService::class, function ($mock): void {
            $mock->shouldNotReceive('requestEmailVerificationCode');
        });

        $email = 'antibot-on-' . uniqid('', false) . '@example.com';

        $this->postJson(route('catalog.collaborators.request-verification-code'), [
            'email' => $email,
            'full_name' => 'Test User',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['antibot']);
    }

    public function test_request_verification_code_accepts_token_when_turnstile_active_and_siteverify_succeeds(): void
    {
        $this->enableTurnstileForTests();

        Http::fake([
            'https://challenges.cloudflare.com/turnstile/v0/siteverify' => Http::response(['success' => true], 200),
        ]);

        $this->mock(CatalogCollaboratorVerificationService::class, function ($mock): void {
            $mock->shouldReceive('requestEmailVerificationCode')
                ->once()
                ->andReturn(['status' => 'sent']);
        });

        $email = 'antibot-token-' . uniqid('', false) . '@example.com';
        $field = (string) config('antibot.verification_request_response_input');

        $this->postJson(route('catalog.collaborators.request-verification-code'), [
            'email' => $email,
            'full_name' => 'Test User',
            $field => 'test-turnstile-token',
        ])
            ->assertOk()
            ->assertJsonFragment(['message' => __('app.collaborator.verify_code_sent')]);

        Http::assertSent(function ($request): bool {
            return str_contains((string) $request->url(), 'challenges.cloudflare.com/turnstile/v0/siteverify');
        });
    }

    public function test_admin_login_rejects_missing_turnstile_when_active(): void
    {
        $this->enableTurnstileForTests();

        $this->get(route('login'));
        $this->post(route('login'), [
            '_token' => session()->token(),
            'username' => 'nobody',
            'password' => 'wrong',
        ])->assertSessionHasErrors('antibot');
    }

    private function enableTurnstileForTests(): void
    {
        config([
            'antibot.driver' => 'turnstile',
            'antibot.turnstile.site_key' => 'test-site-key',
            'antibot.turnstile.secret_key' => 'test-secret-key',
        ]);
        $this->app->forgetInstance(AntiBotVerifier::class);
    }
}
