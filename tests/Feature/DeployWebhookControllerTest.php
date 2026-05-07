<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

final class DeployWebhookControllerTest extends TestCase
{
    public function test_deploy_returns_forbidden_without_bearer_token(): void
    {
        config([
            'deploy.secret' => 'hook-secret',
            'deploy.coolify.deploy_url' => 'http://coolify.internal/api/v1/deploy',
            'deploy.coolify.token' => 'coolify-token',
        ]);

        $this->postJson('/deploy')
            ->assertForbidden();
    }

    public function test_deploy_returns_forbidden_with_invalid_bearer(): void
    {
        config([
            'deploy.secret' => 'hook-secret',
            'deploy.coolify.deploy_url' => 'http://coolify.internal/api/v1/deploy',
            'deploy.coolify.token' => 'coolify-token',
        ]);

        $this->postJson('/deploy', [], ['Authorization' => 'Bearer wrong'])
            ->assertForbidden();
    }

    public function test_deploy_returns_service_unavailable_when_coolify_env_missing(): void
    {
        config([
            'deploy.secret' => 'hook-secret',
            'deploy.coolify.deploy_url' => '',
            'deploy.coolify.token' => '',
        ]);

        $this->postJson('/deploy', [], ['Authorization' => 'Bearer hook-secret'])
            ->assertStatus(503);
    }

    public function test_deploy_calls_coolify_and_returns_ok_when_configured(): void
    {
        config([
            'deploy.secret' => 'hook-secret',
            'deploy.coolify.deploy_url' => 'http://coolify.internal/api/v1/deploy?uuid=x&force=false',
            'deploy.coolify.token' => 'coolify-token',
        ]);

        Http::fake([
            'http://coolify.internal/*' => Http::response(['deployed' => true], 200),
        ]);

        $this->postJson('/deploy', [], ['Authorization' => 'Bearer hook-secret'])
            ->assertOk()
            ->assertJson(['ok' => true]);

        Http::assertSent(function (\Illuminate\Http\Client\Request $request): bool {
            return $request->method() === 'GET'
                && str_starts_with((string) $request->url(), 'http://coolify.internal/api/v1/deploy')
                && $request->hasHeader('Authorization', 'Bearer coolify-token');
        });
    }

    public function test_deploy_returns_bad_gateway_when_coolify_returns_error(): void
    {
        config([
            'deploy.secret' => 'hook-secret',
            'deploy.coolify.deploy_url' => 'http://coolify.internal/api/v1/deploy',
            'deploy.coolify.token' => 'coolify-token',
        ]);

        Http::fake([
            'http://coolify.internal/*' => Http::response(['error' => 'no'], 500),
        ]);

        $this->postJson('/deploy', [], ['Authorization' => 'Bearer hook-secret'])
            ->assertStatus(502)
            ->assertJsonFragment(['message' => 'Coolify deploy request failed.']);
    }
}
