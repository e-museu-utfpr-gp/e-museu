<?php

declare(strict_types=1);

namespace Tests\Unit\Client\Ai;

use App\Client\Ai\AiChatCompletionHttpClient;
use App\Support\Admin\Ai\AdminChatCompletionHttpRequestFactory;
use App\Support\Admin\Ai\ChatCompletionHttpRequest;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

final class AiChatCompletionHttpClientTest extends TestCase
{
    protected function tearDown(): void
    {
        Http::fake();
        parent::tearDown();
    }

    public function test_chat_completion_uses_request_dto_only(): void
    {
        $req = new ChatCompletionHttpRequest(
            'https://api.example.test/v1/chat/completions',
            'token-unit',
            ['X-Custom' => '1'],
            15,
            90,
            0.2,
            2048,
            'UnitPrefix',
            'Unit human',
        );

        Http::fake([
            'https://api.example.test/*' => Http::response([
                'choices' => [
                    ['message' => ['content' => '{"ok":true}']],
                ],
            ], 200),
        ]);

        $client = app(AiChatCompletionHttpClient::class);
        $out = $client->chatCompletion($req, 'sys', 'user', ['model-a']);

        $this->assertSame('{"ok":true}', $out['content']);
        $this->assertSame('model-a', $out['model']);
    }

    public function test_factory_builds_from_config_section(): void
    {
        Config::set('ai.openrouter.api_key', 'sk-factory');
        Config::set('ai.openrouter.provider_url', 'https://openrouter.ai/api/v1/chat/completions');
        Config::set('ai.openrouter.extra_request_headers', [
            'HTTP-Referer' => 'http://localhost',
            'X-OpenRouter-Title' => 'test',
        ]);
        Config::set('ai.openrouter.temperature', 0.2);
        Config::set('ai.openrouter.max_tokens', 2048);
        Config::set('ai.openrouter.timeout_seconds', 90);
        Config::set('ai.openrouter.connect_timeout_seconds', 15);
        Config::set('ai.openrouter.http_error_prefix', 'OpenRouter');
        Config::set('ai.openrouter.human_label', 'OpenRouter');

        Http::fake([
            'https://openrouter.ai/*' => Http::response([
                'choices' => [
                    ['message' => ['content' => '{"name":"ViaFactory"}']],
                ],
            ], 200),
        ]);

        $http = AdminChatCompletionHttpRequestFactory::fromProviderSlug('openrouter');
        $this->assertNotNull($http);

        $client = app(AiChatCompletionHttpClient::class);
        $out = $client->chatCompletion($http, 's', 'u', ['m1']);

        $this->assertSame('{"name":"ViaFactory"}', $out['content']);
    }
}
