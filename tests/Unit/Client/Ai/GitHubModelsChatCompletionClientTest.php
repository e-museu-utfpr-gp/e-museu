<?php

declare(strict_types=1);

namespace Tests\Unit\Client\Ai;

use App\Client\Ai\GitHubModelsChatCompletionClient;
use App\Exceptions\AiTranslationUserException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

final class GitHubModelsChatCompletionClientTest extends TestCase
{
    protected function tearDown(): void
    {
        Http::fake();
        parent::tearDown();
    }

    public function test_chat_completion_returns_content_and_model_on_success(): void
    {
        Config::set('ai.github_models.api_key', 'ghp_unit_test');
        Config::set('ai.github_models.inference_url', 'https://models.github.ai/inference/chat/completions');
        Config::set('ai.github_models.accept_header', 'application/vnd.github+json');
        Config::set('ai.github_models.api_version', '2022-11-28');
        Config::set('ai.github_models.temperature', 0.2);
        Config::set('ai.github_models.max_tokens', 2048);
        Config::set('ai.github_models.timeout_seconds', 90);
        Config::set('ai.github_models.connect_timeout_seconds', 15);

        Http::fake([
            'https://models.github.ai/*' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => '{"name":"GitHubUnit"}',
                        ],
                    ],
                ],
            ], 200),
        ]);

        $client = app(GitHubModelsChatCompletionClient::class);
        $out = $client->chatCompletion('system', 'user', ['openai/gpt-4o-mini']);

        $this->assertSame('{"name":"GitHubUnit"}', $out['content']);
        $this->assertSame('openai/gpt-4o-mini', $out['model']);
    }

    public function test_throws_not_configured_when_token_missing(): void
    {
        Config::set('ai.github_models.api_key', '');

        $client = app(GitHubModelsChatCompletionClient::class);

        $this->expectException(AiTranslationUserException::class);
        $this->expectExceptionMessage('view.admin.ai.not_configured');

        $client->chatCompletion('s', 'u', ['m']);
    }
}
