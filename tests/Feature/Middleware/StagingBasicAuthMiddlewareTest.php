<?php

declare(strict_types=1);

namespace Tests\Feature\Middleware;

/**
 * Covers {@see \App\Http\Middleware\Auth\StagingBasicAuth}.
 */
class StagingBasicAuthMiddlewareTest extends MysqlMiddlewareTestCase
{
    private string $savedStagingBasicUser = '';

    private string $savedStagingBasicPassword = '';

    protected function setUp(): void
    {
        parent::setUp();

        $this->savedStagingBasicUser = (string) config('auth.staging_basic.user');
        $this->savedStagingBasicPassword = (string) config('auth.staging_basic.password');
    }

    protected function tearDown(): void
    {
        config([
            'auth.staging_basic.user' => $this->savedStagingBasicUser,
            'auth.staging_basic.password' => $this->savedStagingBasicPassword,
        ]);

        if ($this->app !== null) {
            $this->app->detectEnvironment(fn (): string => 'testing');
        }

        parent::tearDown();
    }

    public function test_basic_auth_not_required_outside_staging_environment(): void
    {
        config([
            'auth.staging_basic.user' => 'gate_user',
            'auth.staging_basic.password' => 'gate_pass',
        ]);

        $this->get(route('home'))
            ->assertOk();
    }

    public function test_staging_requires_basic_credentials_when_configured(): void
    {
        $this->app->detectEnvironment(fn (): string => 'staging');
        config([
            'auth.staging_basic.user' => 'staging_user',
            'auth.staging_basic.password' => 'staging_pass',
        ]);

        $this->get(route('home'))
            ->assertStatus(401)
            ->assertHeader('WWW-Authenticate');
    }

    public function test_staging_allows_request_with_valid_basic_credentials(): void
    {
        $this->app->detectEnvironment(fn (): string => 'staging');
        config([
            'auth.staging_basic.user' => 'staging_user',
            'auth.staging_basic.password' => 'staging_pass',
        ]);

        $this->withBasicAuth('staging_user', 'staging_pass')
            ->get(route('home'))
            ->assertOk();
    }

    public function test_staging_skips_basic_auth_when_credentials_not_configured(): void
    {
        $this->app->detectEnvironment(fn (): string => 'staging');
        config([
            'auth.staging_basic.user' => '',
            'auth.staging_basic.password' => '',
        ]);

        $this->get(route('home'))
            ->assertOk();
    }
}
