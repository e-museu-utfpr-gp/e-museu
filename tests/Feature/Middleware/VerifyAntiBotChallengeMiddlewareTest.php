<?php

declare(strict_types=1);

namespace Tests\Feature\Middleware;

use App\Http\Middleware\VerifyAntiBotChallenge;
use App\Support\Security\AntiBotVerifier;
use Illuminate\Http\Request;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

/**
 * Covers {@see \App\Http\Middleware\VerifyAntiBotChallenge} behaviour not duplicated elsewhere.
 */
#[Group('middleware')]
class VerifyAntiBotChallengeMiddlewareTest extends TestCase
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

    protected function tearDown(): void
    {
        config([
            'antibot.driver' => 'null',
            'antibot.turnstile.site_key' => '',
            'antibot.turnstile.secret_key' => '',
        ]);
        $this->app->forgetInstance(AntiBotVerifier::class);

        parent::tearDown();
    }

    public function test_unknown_scope_throws_invalid_argument_exception(): void
    {
        $middleware = app(VerifyAntiBotChallenge::class);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown antibot middleware scope');

        $middleware->handle(
            Request::create('/test', 'POST'),
            static function (): \Symfony\Component\HttpFoundation\Response {
                throw new \RuntimeException('next must not run');
            },
            'invalid-scope',
        );
    }

    public function test_verification_request_scope_calls_next_when_antibot_disabled(): void
    {
        $middleware = app(VerifyAntiBotChallenge::class);
        $called = false;

        $response = $middleware->handle(
            Request::create('/test', 'POST'),
            function () use (&$called): \Illuminate\Http\Response {
                $called = true;

                return response('ok', 200);
            },
            'verification-request'
        );

        $this->assertTrue($called);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('ok', $response->getContent());
    }
}
