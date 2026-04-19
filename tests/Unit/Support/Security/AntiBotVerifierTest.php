<?php

namespace Tests\Unit\Support\Security;

use App\Support\Security\AntiBotVerifier;
use Illuminate\Config\Repository as ConfigRepository;
use Illuminate\Http\Request;
use Tests\TestCase;

final class AntiBotVerifierTest extends TestCase
{
    public function test_inactive_when_driver_not_turnstile(): void
    {
        $config = new ConfigRepository([
            'antibot' => [
                'driver' => 'none',
                'response_input' => 'cf-turnstile-response',
            ],
        ]);
        $verifier = new AntiBotVerifier($config);

        $this->assertFalse($verifier->isActive());
        $this->assertSame([], $verifier->challengeViewData());
        $this->assertSame('cf-turnstile-response', $verifier->responseInputName());
    }

    public function test_inactive_when_turnstile_keys_missing(): void
    {
        $config = new ConfigRepository([
            'antibot' => [
                'driver' => 'turnstile',
                'response_input' => 'cf-turnstile-response',
                'turnstile' => [
                    'site_key' => '',
                    'secret_key' => '',
                    'verify_url' => 'https://challenges.cloudflare.com/turnstile/v0/siteverify',
                ],
            ],
        ]);
        $verifier = new AntiBotVerifier($config);

        $this->assertFalse($verifier->isActive());
    }

    public function test_active_exposes_site_key_in_view_data(): void
    {
        $config = new ConfigRepository([
            'antibot' => [
                'driver' => 'turnstile',
                'response_input' => 'cf-turnstile-response',
                'turnstile' => [
                    'site_key' => 'site-key-1',
                    'secret_key' => 'secret-key-1',
                    'verify_url' => 'https://example.test/verify',
                ],
            ],
        ]);
        $verifier = new AntiBotVerifier($config);

        $this->assertTrue($verifier->isActive());
        $this->assertSame(['siteKey' => 'site-key-1'], $verifier->challengeViewData());
    }

    public function test_validate_no_op_when_inactive(): void
    {
        $config = new ConfigRepository([
            'antibot' => [
                'driver' => 'none',
                'response_input' => 'cf-turnstile-response',
            ],
        ]);
        $verifier = new AntiBotVerifier($config);
        $request = Request::create('/', 'POST', []);

        $verifier->validate($request);

        $this->addToAssertionCount(1);
    }
}
