<?php

declare(strict_types=1);

namespace App\Support\Admin\Ai\OpenAiCompatibleChatClientSupport\Concerns;

use App\Exceptions\AiTranslationUserException;
use Illuminate\Http\Client\Response;
use RuntimeException;
use Throwable;

trait DispatchesOpenAiChatHttpResponse
{
    /**
     * @return array{content: string, model: string}|null
     */
    private static function dispatchResponse(
        Response $response,
        string $model,
        string $httpErrorPrefix,
        ?Throwable &$lastThrowable,
    ): ?array {
        // Do not rotate through sibling models on rate limit; escalate to the next provider in the chain.
        if ($response->status() === 429) {
            $rateLimited = new RuntimeException($httpErrorPrefix . ' HTTP 429');
            $lastThrowable = $rateLimited;

            throw new AiTranslationUserException('view.admin.ai.error_rate_limited', [], $rateLimited);
        }

        $retryable = self::throwableForRetryableHttpStatus($response, $httpErrorPrefix);
        if ($retryable !== null) {
            $lastThrowable = $retryable;

            return null;
        }

        self::assertNotForbiddenCredentials($response);

        $clientError = self::throwableForClientError($response, $httpErrorPrefix);
        if ($clientError !== null) {
            $lastThrowable = $clientError;

            return null;
        }

        return self::payloadAfterSuccessfulStatus(
            $response,
            $model,
            $httpErrorPrefix,
            $lastThrowable,
        );
    }

    private static function throwableForRetryableHttpStatus(
        Response $response,
        string $httpErrorPrefix,
    ): ?Throwable {
        if ($response->serverError()) {
            return new RuntimeException($httpErrorPrefix . ' HTTP ' . $response->status());
        }

        return null;
    }

    private static function assertNotForbiddenCredentials(Response $response): void
    {
        if ($response->status() === 401 || $response->status() === 403) {
            throw new AiTranslationUserException('view.admin.ai.error_credentials');
        }
    }

    private static function throwableForClientError(
        Response $response,
        string $httpErrorPrefix,
    ): ?Throwable {
        if (! $response->clientError()) {
            return null;
        }

        $body = $response->body();
        $snippet = $body !== '' && strlen($body) > 2048
            ? substr($body, 0, 2048) . '...[truncated]'
            : $body;

        return new RuntimeException(
            $httpErrorPrefix . ' HTTP ' . $response->status() . ' ' . $snippet
        );
    }
}
