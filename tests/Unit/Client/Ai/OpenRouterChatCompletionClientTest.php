<?php

declare(strict_types=1);

namespace Tests\Unit\Client\Ai;

use App\Client\Ai\OpenRouterChatCompletionClient;
use App\Exceptions\AiTranslationUserException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

final class OpenRouterChatCompletionClientTest extends TestCase
{
    protected function tearDown(): void
    {
        Http::fake();
        parent::tearDown();
    }

    public function test_chat_completion_returns_content_and_model_on_success(): void
    {
        Config::set('ai.openrouter.api_key', 'sk-unit-test');
        Config::set('ai.openrouter.base_url', 'https://openrouter.ai/api/v1');
        Config::set('ai.openrouter.temperature', 0.2);
        Config::set('ai.openrouter.max_tokens', 2048);
        Config::set('ai.openrouter.timeout_seconds', 90);
        Config::set('ai.openrouter.connect_timeout_seconds', 15);
        Config::set('app.url', 'http://localhost');
        Config::set('app.name', 'e-museu-test');

        Http::fake([
            'https://openrouter.ai/*' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => '{"name":"Unit"}',
                        ],
                    ],
                ],
            ], 200),
        ]);

        $client = app(OpenRouterChatCompletionClient::class);
        $out = $client->chatCompletion('system', 'user', ['test/model-one']);

        $this->assertSame('{"name":"Unit"}', $out['content']);
        $this->assertSame('test/model-one', $out['model']);
    }

    public function test_throws_not_configured_when_api_key_missing(): void
    {
        Config::set('ai.openrouter.api_key', '');

        $client = app(OpenRouterChatCompletionClient::class);

        $this->expectException(AiTranslationUserException::class);
        $this->expectExceptionMessage('view.admin.ai.not_configured');

        $client->chatCompletion('s', 'u', ['m']);
    }
}
