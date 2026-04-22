<?php

declare(strict_types=1);

namespace Tests\Unit\Client\Ai;

use App\Client\Ai\GroqChatCompletionClient;
use App\Exceptions\AiTranslationUserException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

final class GroqChatCompletionClientTest extends TestCase
{
    protected function tearDown(): void
    {
        Http::fake();
        parent::tearDown();
    }

    public function test_chat_completion_returns_content_and_model_on_success(): void
    {
        Config::set('ai.groq.api_key', 'gsk-unit-test');
        Config::set('ai.groq.base_url', 'https://api.groq.com/openai/v1');
        Config::set('ai.groq.temperature', 0.2);
        Config::set('ai.groq.max_tokens', 2048);
        Config::set('ai.groq.timeout_seconds', 90);
        Config::set('ai.groq.connect_timeout_seconds', 15);

        Http::fake([
            'https://api.groq.com/*' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => '{"name":"GroqUnit"}',
                        ],
                    ],
                ],
            ], 200),
        ]);

        $client = app(GroqChatCompletionClient::class);
        $out = $client->chatCompletion('system', 'user', ['llama-unit']);

        $this->assertSame('{"name":"GroqUnit"}', $out['content']);
        $this->assertSame('llama-unit', $out['model']);
    }

    public function test_throws_not_configured_when_api_key_missing(): void
    {
        Config::set('ai.groq.api_key', '');

        $client = app(GroqChatCompletionClient::class);

        $this->expectException(AiTranslationUserException::class);
        $this->expectExceptionMessage('view.admin.ai.not_configured');

        $client->chatCompletion('s', 'u', ['m']);
    }
}
