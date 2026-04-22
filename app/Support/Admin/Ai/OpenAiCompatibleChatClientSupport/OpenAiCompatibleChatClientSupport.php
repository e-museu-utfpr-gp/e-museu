<?php

declare(strict_types=1);

namespace App\Support\Admin\Ai\OpenAiCompatibleChatClientSupport;

use App\Exceptions\AiTranslationUserException;
use App\Support\Admin\Ai\OpenAiCompatibleChatClientSupport\Concerns\BuildsOpenAiCompatibleChatRequest;
use App\Support\Admin\Ai\OpenAiCompatibleChatClientSupport\Concerns\ClassifiesOpenAiChatModelFailures;
use App\Support\Admin\Ai\OpenAiCompatibleChatClientSupport\Concerns\DispatchesOpenAiChatHttpResponse;
use App\Support\Admin\Ai\OpenAiCompatibleChatClientSupport\Concerns\ParsesOpenAiCompatibleChatJsonPayload;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * OpenAI-compatible chat HTTP clients: multi-model loop, request body, status handling, JSON payload.
 *
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
final class OpenAiCompatibleChatClientSupport
{
    use BuildsOpenAiCompatibleChatRequest;
    use ClassifiesOpenAiChatModelFailures;
    use DispatchesOpenAiChatHttpResponse;
    use ParsesOpenAiCompatibleChatJsonPayload;

    /**
     * @param  list<string>  $models
     * @return array{content: string, model: string}
     *
     * @throws AiTranslationUserException When every model fails or the response is unusable.
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function completeAcrossModels(
        PendingRequest $pending,
        string $url,
        string $systemPrompt,
        string $userPrompt,
        array $models,
        float $temperature,
        int $maxTokens,
        string $httpErrorPrefix,
        string $providerHumanLabel,
    ): array {
        self::assertModelsNonEmpty($models);

        $lastThrowable = null;
        /** @var list<array{model: string, error: string}> $attemptFailures */
        $attemptFailures = [];

        foreach ($models as $model) {
            $model = trim($model);
            if ($model === '') {
                continue;
            }

            try {
                $response = $pending->post($url, self::chatRequestBody(
                    $model,
                    $systemPrompt,
                    $userPrompt,
                    $temperature,
                    $maxTokens,
                ));

                $payload = self::dispatchResponse(
                    $response,
                    $model,
                    $httpErrorPrefix,
                    $lastThrowable,
                );
                if ($payload !== null) {
                    return $payload;
                }
                $attemptFailures[] = [
                    'model' => $model,
                    'error' => $lastThrowable?->getMessage() ?? 'unknown',
                ];
            } catch (AiTranslationUserException $e) {
                throw $e;
            } catch (Throwable $e) {
                $lastThrowable = $e;
                $attemptFailures[] = ['model' => $model, 'error' => $e->getMessage()];
            }
        }

        $translationKey = self::translationKeyWhenAllModelsFailed(
            $attemptFailures,
            $httpErrorPrefix,
        );

        Log::warning($providerHumanLabel . ': all configured models failed for chat completion.', [
            'last' => $lastThrowable?->getMessage(),
            'attempts' => $attemptFailures,
            'user_message_key' => $translationKey,
        ]);

        throw new AiTranslationUserException(
            $translationKey,
            [],
            $lastThrowable
        );
    }
}
