<?php

declare(strict_types=1);

namespace Tests\Feature\Admin\Ai;

use App\Models\Identity\Admin;
use App\Support\Admin\Ai\AdminAi;
use App\Support\Admin\Ai\AdminContentTranslationRegistry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

final class AdminContentTranslationControllerTest extends TestCase
{
    use RefreshDatabase;

    /** URL pattern aligned with the default OpenRouter completions URL in {@see \App\Client\Ai\OpenRouterChatCompletionClient}. */
    private const OPENROUTER_COMPLETIONS_URL_PATTERN = 'https://openrouter.ai/*';

    /** Groq OpenAI-compatible completions base in {@see \App\Client\Ai\GroqChatCompletionClient}. */
    private const GROQ_COMPLETIONS_URL_PATTERN = 'https://api.groq.com/*';

    /** GitHub Models inference in {@see \App\Client\Ai\GitHubModelsChatCompletionClient}. */
    private const GITHUB_MODELS_COMPLETIONS_URL_PATTERN = 'https://models.github.ai/*';

    protected function setUp(): void
    {
        parent::setUp();

        AdminAi::resetMissingProvidersWarningForTesting();

        Config::set('ai.github_models.api_key', '');
        Config::set('ai.groq.api_key', '');
    }

    public function test_guest_cannot_translate(): void
    {
        $response = $this->postJson(route('admin.ai.translate-content'), [
            'resource' => AdminContentTranslationRegistry::RESOURCE_TAG,
            'target_locale' => 'en',
            'mode' => 'fill',
            'translations' => [
                'pt_BR' => ['name' => 'Olá'],
                'en' => ['name' => ''],
            ],
        ]);

        $response->assertUnauthorized();
    }

    public function test_returns_503_when_ai_disabled(): void
    {
        Config::set('ai.github_models.enabled', false);
        Config::set('ai.openrouter.enabled', false);
        Config::set('ai.groq.enabled', false);
        Config::set('ai.openrouter.api_key', 'sk-test');

        $admin = $this->makeAdmin();
        $response = $this->actingAs($admin)->postJson(route('admin.ai.translate-content'), $this->minimalPayload());

        $response->assertStatus(503);
        $response->assertJsonFragment(['message' => __('view.admin.ai.disabled')]);
    }

    public function test_returns_503_when_api_key_missing(): void
    {
        Config::set('ai.openrouter.api_key', '');

        $admin = $this->makeAdmin();
        $response = $this->actingAs($admin)->postJson(route('admin.ai.translate-content'), $this->minimalPayload());

        $response->assertStatus(503);
        $response->assertJsonFragment(['message' => __('view.admin.ai.not_configured')]);
    }

    public function test_translates_tag_name_with_groq_only_when_openrouter_key_missing(): void
    {
        Config::set('ai.openrouter.api_key', '');
        Config::set('ai.openrouter.models', ['ignored/model']);
        Config::set('ai.groq.api_key', 'gsk-test');
        Config::set('ai.groq.models', ['llama-3.1-8b-instant']);
        Config::set('ai.groq.enabled', true);

        Http::fake([
            self::GROQ_COMPLETIONS_URL_PATTERN => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => '{"name":"Groq only"}',
                        ],
                    ],
                ],
            ], 200),
        ]);

        $admin = $this->makeAdmin();
        $response = $this->actingAs($admin)->postJson(route('admin.ai.translate-content'), $this->minimalPayload());

        $response->assertOk();
        $response->assertJsonPath('translations.name', 'Groq only');
        Http::assertSentCount(1);
    }

    public function test_skips_openrouter_when_disabled_even_if_api_key_is_set(): void
    {
        Config::set('ai.openrouter.enabled', false);
        Config::set('ai.openrouter.api_key', 'sk-would-fail-if-used');
        Config::set('ai.openrouter.models', ['model/would-404']);
        Config::set('ai.groq.api_key', 'gsk-test');
        Config::set('ai.groq.models', ['llama-3.1-8b-instant']);
        Config::set('ai.groq.enabled', true);

        Http::fake([
            self::GROQ_COMPLETIONS_URL_PATTERN => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => '{"name":"Groq with OpenRouter off"}',
                        ],
                    ],
                ],
            ], 200),
        ]);

        $admin = $this->makeAdmin();
        $response = $this->actingAs($admin)->postJson(route('admin.ai.translate-content'), $this->minimalPayload());

        $response->assertOk();
        $response->assertJsonPath('translations.name', 'Groq with OpenRouter off');
        Http::assertSentCount(1);
    }

    public function test_translates_tag_name_with_openrouter_fake(): void
    {
        Config::set('ai.openrouter.api_key', 'sk-test');
        Config::set('ai.openrouter.models', ['test/model']);

        Http::fake([
            self::OPENROUTER_COMPLETIONS_URL_PATTERN => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => '{"name":"Hello"}',
                        ],
                    ],
                ],
            ], 200),
        ]);

        $admin = $this->makeAdmin();
        $response = $this->actingAs($admin)->postJson(route('admin.ai.translate-content'), $this->minimalPayload());

        $response->assertOk();
        $response->assertJsonPath('translations.name', 'Hello');
    }

    public function test_openrouter_rotates_to_second_model_when_first_returns_server_error(): void
    {
        Config::set('ai.openrouter.api_key', 'sk-test');
        Config::set('ai.openrouter.models', ['model/alpha', 'model/beta']);

        Http::fake([
            self::OPENROUTER_COMPLETIONS_URL_PATTERN => Http::sequence()
                ->push(['error' => 'upstream'], 503)
                ->push([
                    'choices' => [
                        [
                            'message' => [
                                'content' => '{"name":"From second model"}',
                            ],
                        ],
                    ],
                ], 200),
        ]);

        $admin = $this->makeAdmin();
        $response = $this->actingAs($admin)->postJson(route('admin.ai.translate-content'), $this->minimalPayload());

        $response->assertOk();
        $response->assertJsonPath('translations.name', 'From second model');

        Http::assertSentCount(2);
        Http::assertSentInOrder([
            function (Request $request, ?Response $_response): bool {
                $data = json_decode($request->body(), true);

                return is_array($data) && ($data['model'] ?? null) === 'model/alpha';
            },
            function (Request $request, ?Response $_response): bool {
                $data = json_decode($request->body(), true);

                return is_array($data) && ($data['model'] ?? null) === 'model/beta';
            },
        ]);
    }

    public function test_openrouter_rotates_when_first_returns_no_usable_assistant_message(): void
    {
        Config::set('ai.openrouter.api_key', 'sk-test');
        Config::set('ai.openrouter.models', ['model/one', 'model/two']);

        Http::fake([
            self::OPENROUTER_COMPLETIONS_URL_PATTERN => Http::sequence()
                ->push([
                    'choices' => [
                        ['message' => ['content' => null]],
                    ],
                ], 200)
                ->push([
                    'choices' => [
                        [
                            'message' => [
                                'content' => '{"name":"Recovered"}',
                            ],
                        ],
                    ],
                ], 200),
        ]);

        $admin = $this->makeAdmin();
        $response = $this->actingAs($admin)->postJson(route('admin.ai.translate-content'), $this->minimalPayload());

        $response->assertOk();
        $response->assertJsonPath('translations.name', 'Recovered');
        Http::assertSentCount(2);
    }

    public function test_openrouter_all_models_fail_returns_422_with_message(): void
    {
        Config::set('ai.openrouter.api_key', 'sk-test');
        Config::set('ai.openrouter.models', ['model/x', 'model/y']);

        Http::fake([
            self::OPENROUTER_COMPLETIONS_URL_PATTERN => Http::sequence()
                ->push(['error' => 'rate limited'], 429)
                ->push(['error' => 'overload'], 502),
        ]);

        $admin = $this->makeAdmin();
        $response = $this->actingAs($admin)->postJson(route('admin.ai.translate-content'), $this->minimalPayload());

        $response->assertStatus(422);
        $response->assertJsonFragment(['message' => __('view.admin.ai.error_all_models_failed')]);
        Http::assertSentCount(2);
    }

    public function test_openrouter_all_models_return_422_with_rate_limit_message_when_only_429(): void
    {
        Config::set('ai.openrouter.api_key', 'sk-test');
        Config::set('ai.openrouter.models', ['model/a', 'model/b']);

        Http::fake([
            self::OPENROUTER_COMPLETIONS_URL_PATTERN => Http::sequence()
                ->push(['error' => 'rate limited'], 429)
                ->push(['error' => 'rate limited'], 429),
        ]);

        $admin = $this->makeAdmin();
        $response = $this->actingAs($admin)->postJson(route('admin.ai.translate-content'), $this->minimalPayload());

        $response->assertStatus(422);
        $response->assertJsonFragment(['message' => __('view.admin.ai.error_rate_limited')]);
        Http::assertSentCount(2);
    }

    public function test_openrouter_rate_limited_then_groq_fails_user_gets_groq_message_not_rate_limit_copy(): void
    {
        Config::set('ai.openrouter.api_key', 'sk-test');
        Config::set('ai.openrouter.models', ['or/a', 'or/b']);
        Config::set('ai.groq.api_key', 'gsk-test');
        Config::set('ai.groq.models', ['g/x', 'g/y']);
        Config::set('ai.groq.enabled', true);

        Http::fake([
            self::OPENROUTER_COMPLETIONS_URL_PATTERN => Http::sequence()
                ->push(['error' => 'rate limited'], 429)
                ->push(['error' => 'rate limited'], 429),
            self::GROQ_COMPLETIONS_URL_PATTERN => Http::sequence()
                ->push(['error' => 'bad gateway'], 502)
                ->push(['error' => 'bad gateway'], 503),
        ]);

        $admin = $this->makeAdmin();
        $response = $this->actingAs($admin)->postJson(route('admin.ai.translate-content'), $this->minimalPayload());

        $response->assertStatus(422);
        $response->assertJsonFragment(['message' => __('view.admin.ai.provider_error')]);
        $response->assertJsonMissing(['message' => __('view.admin.ai.error_rate_limited')]);
        Http::assertSentCount(4);
    }

    public function test_openrouter_and_groq_all_rate_limited_returns_rate_limit_message(): void
    {
        Config::set('ai.openrouter.api_key', 'sk-test');
        Config::set('ai.openrouter.models', ['or/a', 'or/b']);
        Config::set('ai.groq.api_key', 'gsk-test');
        Config::set('ai.groq.models', ['g/x', 'g/y']);
        Config::set('ai.groq.enabled', true);

        Http::fake([
            self::OPENROUTER_COMPLETIONS_URL_PATTERN => Http::sequence()
                ->push(['error' => 'rate limited'], 429)
                ->push(['error' => 'rate limited'], 429),
            self::GROQ_COMPLETIONS_URL_PATTERN => Http::sequence()
                ->push(['error' => 'rate limited'], 429)
                ->push(['error' => 'rate limited'], 429),
        ]);

        $admin = $this->makeAdmin();
        $response = $this->actingAs($admin)->postJson(route('admin.ai.translate-content'), $this->minimalPayload());

        $response->assertStatus(422);
        $response->assertJsonFragment(['message' => __('view.admin.ai.error_rate_limited')]);
        Http::assertSentCount(4);
    }

    public function test_openrouter_all_models_no_endpoints_returns_models_unavailable_message(): void
    {
        Config::set('ai.openrouter.api_key', 'sk-test');
        Config::set('ai.openrouter.models', ['old/model-a:free', 'old/model-b:free']);

        $body = '{"error":{"message":"No endpoints found for old/model-a:free.","code":404}}';

        Http::fake([
            self::OPENROUTER_COMPLETIONS_URL_PATTERN => Http::sequence()
                ->push($body, 404)
                ->push(str_replace('model-a', 'model-b', $body), 404),
        ]);

        $admin = $this->makeAdmin();
        $response = $this->actingAs($admin)->postJson(route('admin.ai.translate-content'), $this->minimalPayload());

        $response->assertStatus(422);
        $response->assertJsonFragment(['message' => __('view.admin.ai.error_models_unavailable')]);
        Http::assertSentCount(2);
    }

    public function test_openrouter_all_models_server_error_returns_provider_error_message(): void
    {
        Config::set('ai.openrouter.api_key', 'sk-test');
        Config::set('ai.openrouter.models', ['model/x', 'model/y']);

        Http::fake([
            self::OPENROUTER_COMPLETIONS_URL_PATTERN => Http::sequence()
                ->push(['error' => 'bad gateway'], 502)
                ->push(['error' => 'bad gateway'], 503),
        ]);

        $admin = $this->makeAdmin();
        $response = $this->actingAs($admin)->postJson(route('admin.ai.translate-content'), $this->minimalPayload());

        $response->assertStatus(422);
        $response->assertJsonFragment(['message' => __('view.admin.ai.provider_error')]);
        Http::assertSentCount(2);
    }

    public function test_falls_back_from_openrouter_to_github_when_groq_is_disabled(): void
    {
        Config::set('ai.github_models.api_key', 'ghp_test_models_scope');
        Config::set('ai.github_models.models', ['github/model-a']);
        Config::set('ai.github_models.enabled', true);
        Config::set('ai.openrouter.api_key', 'sk-or-test');
        Config::set('ai.openrouter.models', ['or/one', 'or/two']);
        Config::set('ai.groq.enabled', false);

        Http::fake([
            self::OPENROUTER_COMPLETIONS_URL_PATTERN => Http::sequence()
                ->push(['error' => 'rate limited'], 429)
                ->push(['error' => 'rate limited'], 429),
            self::GITHUB_MODELS_COMPLETIONS_URL_PATTERN => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => '{"name":"From GitHub after OpenRouter"}',
                        ],
                    ],
                ],
            ], 200),
        ]);

        $admin = $this->makeAdmin();
        $response = $this->actingAs($admin)->postJson(route('admin.ai.translate-content'), $this->minimalPayload());

        $response->assertOk();
        $response->assertJsonPath('translations.name', 'From GitHub after OpenRouter');
        Http::assertSentCount(3);
    }

    public function test_falls_back_openrouter_then_groq_then_github(): void
    {
        Config::set('ai.github_models.api_key', 'ghp_test_models_scope');
        Config::set('ai.github_models.models', ['github/last']);
        Config::set('ai.github_models.enabled', true);
        Config::set('ai.openrouter.api_key', 'sk-or-test');
        Config::set('ai.openrouter.models', ['or/a', 'or/b']);
        Config::set('ai.groq.api_key', 'gsk-test');
        Config::set('ai.groq.models', ['llama-a', 'llama-b']);
        Config::set('ai.groq.enabled', true);

        Http::fake([
            self::OPENROUTER_COMPLETIONS_URL_PATTERN => Http::sequence()
                ->push(['error' => 'rate limited'], 429)
                ->push(['error' => 'rate limited'], 429),
            self::GROQ_COMPLETIONS_URL_PATTERN => Http::sequence()
                ->push(['error' => 'rate limited'], 429)
                ->push(['error' => 'rate limited'], 429),
            self::GITHUB_MODELS_COMPLETIONS_URL_PATTERN => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => '{"name":"From GitHub third hop"}',
                        ],
                    ],
                ],
            ], 200),
        ]);

        $admin = $this->makeAdmin();
        $response = $this->actingAs($admin)->postJson(route('admin.ai.translate-content'), $this->minimalPayload());

        $response->assertOk();
        $response->assertJsonPath('translations.name', 'From GitHub third hop');
        Http::assertSentCount(5);
    }

    public function test_falls_back_to_groq_when_openrouter_exhausts_models(): void
    {
        Config::set('ai.openrouter.api_key', 'sk-or-test');
        Config::set('ai.openrouter.models', ['model/a', 'model/b']);
        Config::set('ai.groq.api_key', 'gsk-test');
        Config::set('ai.groq.models', ['llama-3.1-8b-instant']);
        Config::set('ai.groq.enabled', true);

        Http::fake([
            self::OPENROUTER_COMPLETIONS_URL_PATTERN => Http::sequence()
                ->push(['error' => 'rate limited'], 429)
                ->push(['error' => 'rate limited'], 429),
            self::GROQ_COMPLETIONS_URL_PATTERN => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => '{"name":"From Groq"}',
                        ],
                    ],
                ],
            ], 200),
        ]);

        $admin = $this->makeAdmin();
        $response = $this->actingAs($admin)->postJson(route('admin.ai.translate-content'), $this->minimalPayload());

        $response->assertOk();
        $response->assertJsonPath('translations.name', 'From Groq');
        Http::assertSentCount(3);
    }

    public function test_openrouter_401_returns_credentials_error(): void
    {
        Config::set('ai.openrouter.api_key', 'sk-test');
        Config::set('ai.openrouter.models', ['model/single']);
        Config::set('ai.groq.api_key', 'gsk-test');
        Config::set('ai.groq.models', ['llama-3.1-8b-instant']);

        Http::fake([
            self::OPENROUTER_COMPLETIONS_URL_PATTERN => Http::response(
                ['error' => ['message' => 'Invalid bearer']],
                401,
            ),
        ]);

        $admin = $this->makeAdmin();
        $response = $this->actingAs($admin)->postJson(route('admin.ai.translate-content'), $this->minimalPayload());

        $response->assertStatus(422);
        $response->assertJsonFragment(['message' => __('view.admin.ai.error_credentials')]);
        Http::assertSentCount(1);
    }

    public function test_returns_422_when_model_returns_only_empty_strings(): void
    {
        Config::set('ai.openrouter.api_key', 'sk-test');
        Config::set('ai.openrouter.models', ['model/only']);

        Http::fake([
            self::OPENROUTER_COMPLETIONS_URL_PATTERN => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => '{"name":"   "}',
                        ],
                    ],
                ],
            ], 200),
        ]);

        $admin = $this->makeAdmin();
        $response = $this->actingAs($admin)->postJson(route('admin.ai.translate-content'), $this->minimalPayload());

        $response->assertStatus(422);
        $response->assertJsonFragment(['message' => __('view.admin.ai.error_model_empty')]);
        Http::assertSentCount(1);
    }

    public function test_returns_422_when_no_cross_locale_source_for_translation(): void
    {
        Config::set('ai.openrouter.api_key', 'sk-test');
        Config::set('ai.openrouter.models', ['model/only']);

        Http::fake();

        $admin = $this->makeAdmin();
        $response = $this->actingAs($admin)->postJson(route('admin.ai.translate-content'), [
            'resource' => AdminContentTranslationRegistry::RESOURCE_TAG,
            'target_locale' => 'en',
            'mode' => 'fill',
            'translations' => [
                'pt_BR' => ['name' => ''],
                'en' => ['name' => ''],
                'universal' => ['name' => ''],
            ],
        ]);

        $response->assertStatus(422);
        $response->assertJsonFragment(['message' => __('view.admin.ai.error_no_source')]);
        Http::assertNothingSent();
    }

    /**
     * @return array<string, mixed>
     */
    private function minimalPayload(): array
    {
        return [
            'resource' => AdminContentTranslationRegistry::RESOURCE_TAG,
            'target_locale' => 'en',
            'mode' => 'fill',
            'translations' => [
                'pt_BR' => ['name' => 'Etiqueta'],
                'en' => ['name' => ''],
                'universal' => ['name' => ''],
            ],
        ];
    }

    private function makeAdmin(): Admin
    {
        return Admin::create([
            'username' => 'ai_translate_' . uniqid('', false),
            'password' => Hash::make('secret'),
        ]);
    }
}
