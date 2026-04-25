<?php

declare(strict_types=1);

namespace App\Support\Admin\Ai\OpenAiCompatibleChatClientSupport\Concerns;

use Illuminate\Http\Client\Response;
use JsonException;
use RuntimeException;
use Throwable;

trait ParsesOpenAiCompatibleChatJsonPayload
{
    /**
     * @return array{content: string, model: string}|null
     */
    private static function payloadAfterSuccessfulStatus(
        Response $response,
        string $model,
        string $httpErrorPrefix,
        ?Throwable &$lastThrowable,
    ): ?array {
        /** @var array<string, mixed>|null $json */
        $json = $response->json();
        if (! is_array($json)) {
            $lastThrowable = new RuntimeException($httpErrorPrefix . ' response not JSON');

            return null;
        }

        if (self::jsonBodyDeclaresChatError($json)) {
            $lastThrowable = new RuntimeException(
                $httpErrorPrefix . ' error: ' . self::chatErrorMessageFromJson($json)
            );

            return null;
        }

        $content = self::extractAssistantMessageContent($json);
        if ($content === null) {
            $lastThrowable = new RuntimeException($httpErrorPrefix . ' missing assistant content');

            return null;
        }

        return ['content' => $content, 'model' => $model];
    }

    /**
     * @param  array<string, mixed>  $json
     */
    private static function jsonBodyDeclaresChatError(array $json): bool
    {
        $err = $json['error'] ?? null;
        if (is_string($err)) {
            return $err !== '';
        }
        if (is_array($err)) {
            return isset($err['message']) || array_key_exists('code', $err);
        }

        return false;
    }

    /**
     * @param  array<string, mixed>  $json
     */
    private static function chatErrorMessageFromJson(array $json): string
    {
        $err = $json['error'] ?? null;
        if (is_string($err)) {
            return $err;
        }
        if (! is_array($err)) {
            return 'unknown';
        }
        $msg = $err['message'] ?? null;

        return is_string($msg) ? $msg : 'unknown';
    }

    /**
     * @param  array<string, mixed>  $json
     */
    private static function extractAssistantMessageContent(array $json): ?string
    {
        $choices = $json['choices'] ?? null;
        if (! is_array($choices) || $choices === []) {
            return null;
        }

        $first = $choices[0];
        if (! is_array($first)) {
            return null;
        }

        $message = $first['message'] ?? null;
        if (! is_array($message)) {
            return null;
        }

        $content = $message['content'] ?? null;
        if (is_string($content)) {
            return $content;
        }
        if (is_array($content)) {
            try {
                return json_encode($content, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
            } catch (JsonException) {
                return null;
            }
        }

        return null;
    }
}
