<?php

namespace App\Support\Security;

use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

/**
 * Cloudflare Turnstile when {@see isActive()}; otherwise validate is a no-op and no widget data is exposed.
 */
final class AntiBotVerifier
{
    private readonly string $siteKey;

    private readonly string $secretKey;

    private readonly string $verifyUrl;

    private readonly string $responseInputName;

    public function __construct(ConfigRepository $config)
    {
        $this->responseInputName = (string) $config->get('antibot.response_input');
        if ($config->get('antibot.driver') !== 'turnstile') {
            $this->siteKey = '';
            $this->secretKey = '';
            $this->verifyUrl = '';

            return;
        }

        $siteKey = trim((string) $config->get('antibot.turnstile.site_key'));
        $secretKey = trim((string) $config->get('antibot.turnstile.secret_key'));
        if ($siteKey === '' || $secretKey === '') {
            $this->siteKey = '';
            $this->secretKey = '';
            $this->verifyUrl = '';

            return;
        }

        $this->siteKey = $siteKey;
        $this->secretKey = $secretKey;
        $this->verifyUrl = (string) $config->get('antibot.turnstile.verify_url');
    }

    public function isActive(): bool
    {
        return $this->siteKey !== '' && $this->secretKey !== '';
    }

    /**
     * @return array<string, mixed>
     */
    public function challengeViewData(): array
    {
        return $this->isActive() ? ['siteKey' => $this->siteKey] : [];
    }

    public function responseInputName(): string
    {
        return $this->responseInputName;
    }

    /**
     * @param  string|null  $responseInputOverride  e.g. {@see config('antibot.verification_request_response_input')}
     */
    public function validate(Request $request, ?string $responseInputOverride = null): void
    {
        if (! $this->isActive()) {
            return;
        }

        $field = $responseInputOverride ?? $this->responseInputName;
        $token = $request->input($field);
        if (! is_string($token) || trim($token) === '') {
            $this->fail();
        }

        try {
            $response = Http::asForm()
                ->timeout(10)
                ->connectTimeout(5)
                ->post($this->verifyUrl, [
                    'secret' => $this->secretKey,
                    'response' => $token,
                    'remoteip' => $request->ip(),
                ]);
        } catch (ConnectionException $e) {
            Log::warning('Anti-bot Turnstile siteverify request failed (network/timeout).', [
                'exception' => $e::class,
                'message' => $e->getMessage(),
            ]);
            $this->fail();
        }

        if (! $response->successful()) {
            $this->fail();
        }

        $payload = $response->json();
        if (! is_array($payload) || empty($payload['success'])) {
            $this->fail();
        }
    }

    private function fail(): never
    {
        throw ValidationException::withMessages([
            'antibot' => [__('antibot.challenge_failed')],
        ]);
    }
}
