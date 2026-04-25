<?php

declare(strict_types=1);

namespace Tests\Feature\Catalog;

use App\Services\Collaborator\CatalogCollaboratorVerificationService;
use Tests\TestCase;

/**
 * Contract: HTTP JSON for mail-unavailable paths; uses mocked
 * {@see CatalogCollaboratorVerificationService} (no outbound mail).
 * Message detail vs generic copy depends on {@see CatalogVerifyMailError} rules exercised through the route.
 */
class CatalogVerifyMailErrorRouteTest extends TestCase
{
    private string $savedAppEnv = '';

    private bool $savedAppDebug = false;

    protected function setUp(): void
    {
        parent::setUp();
        config(['mail.public_contribution_email_verification_enabled' => true]);
        $this->savedAppEnv = (string) config('app.env');
        $this->savedAppDebug = (bool) config('app.debug');
    }

    protected function tearDown(): void
    {
        config(['app.env' => $this->savedAppEnv, 'app.debug' => $this->savedAppDebug]);
        parent::tearDown();
    }

    public function test_mail_not_configured_when_not_masked_shows_detail(): void
    {
        config(['app.env' => 'testing', 'app.debug' => true]);
        $this->mock(CatalogCollaboratorVerificationService::class, function ($m) {
            $m->shouldReceive('requestEmailVerificationCode')->once()->andReturn(['status' => 'mail_not_configured']);
        });

        $this->postJson(route('catalog.collaborators.request-verification-code'), [
            'email' => 'a@b.com',
            'full_name' => 'Name',
        ])
            ->assertStatus(422)
            ->assertJsonFragment(['message' => __('app.collaborator.verify_mail_not_configured')]);
    }

    public function test_mail_not_configured_when_masked_uses_generic_message(): void
    {
        config(['app.env' => 'production', 'app.debug' => false]);
        $this->mock(CatalogCollaboratorVerificationService::class, function ($m) {
            $m->shouldReceive('requestEmailVerificationCode')->once()->andReturn(['status' => 'mail_not_configured']);
        });

        $this->postJson(route('catalog.collaborators.request-verification-code'), [
            'email' => 'a@b.com',
            'full_name' => 'Name',
        ])
            ->assertStatus(422)
            ->assertJsonFragment(['message' => __('app.collaborator.verify_service_unavailable')]);
    }

    public function test_send_failed_when_not_masked_shows_detail(): void
    {
        config(['app.env' => 'testing', 'app.debug' => true]);
        $this->mock(CatalogCollaboratorVerificationService::class, function ($m) {
            $m->shouldReceive('requestEmailVerificationCode')->once()->andReturn(['status' => 'send_failed']);
        });

        $this->postJson(route('catalog.collaborators.request-verification-code'), [
            'email' => 'a@b.com',
            'full_name' => 'Name',
        ])
            ->assertStatus(422)
            ->assertJsonFragment(['message' => __('app.collaborator.verify_mail_send_failed')]);
    }
}
