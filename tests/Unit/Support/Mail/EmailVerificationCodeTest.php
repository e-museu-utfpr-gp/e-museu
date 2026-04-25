<?php

declare(strict_types=1);

namespace Tests\Unit\Support\Mail;

use App\Support\Mail\EmailVerificationCode;
use Tests\TestCase;

final class EmailVerificationCodeTest extends TestCase
{
    private mixed $savedAppKey = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->savedAppKey = config('app.key');
    }

    protected function tearDown(): void
    {
        config(['app.key' => $this->savedAppKey]);

        parent::tearDown();
    }

    public function test_generate_plain_code_is_six_digits(): void
    {
        $code = EmailVerificationCode::generatePlainCode();

        $this->assertSame(6, strlen($code));
        $this->assertMatchesRegularExpression('/^\d{6}$/', $code);
    }

    public function test_hash_plain_code_uses_app_key(): void
    {
        config(['app.key' => 'unit-test-app-key']);

        $expected = hash('sha256', '123456' . config('app.key'));

        $this->assertSame($expected, EmailVerificationCode::hashPlainCode('123456'));
    }

    public function test_generate_returns_code_and_matching_hash(): void
    {
        config(['app.key' => 'unit-test-app-key']);

        $pair = EmailVerificationCode::generate();

        $this->assertArrayHasKey('code', $pair);
        $this->assertArrayHasKey('hash', $pair);
        $this->assertSame(EmailVerificationCode::hashPlainCode($pair['code']), $pair['hash']);
    }
}
