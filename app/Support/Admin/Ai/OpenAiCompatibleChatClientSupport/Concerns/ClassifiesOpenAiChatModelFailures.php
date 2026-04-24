<?php

declare(strict_types=1);

namespace App\Support\Admin\Ai\OpenAiCompatibleChatClientSupport\Concerns;

trait ClassifiesOpenAiChatModelFailures
{
    /**
     * @param  list<array{model: string, error: string}>  $attemptFailures
     */
    private static function translationKeyWhenAllModelsFailed(
        array $attemptFailures,
        string $httpErrorPrefix,
    ): string {
        if ($attemptFailures === []) {
            return 'view.admin.ai.error_all_models_failed';
        }

        $errors = array_column($attemptFailures, 'error');

        if (self::eachErrorMatches($errors, static fn (string $e): bool => str_contains($e, 'HTTP 429'))) {
            return 'view.admin.ai.error_rate_limited';
        }

        if (self::eachErrorMatches($errors, static fn (string $e): bool => self::errorLooksLikeModelUnavailable($e))) {
            return 'view.admin.ai.error_models_unavailable';
        }

        $fivexxPattern = '/' . preg_quote($httpErrorPrefix, '/') . ' HTTP 5\\d\\d/';
        if (self::eachErrorMatches($errors, static fn (string $e): bool => (bool) preg_match($fivexxPattern, $e))) {
            return 'view.admin.ai.provider_error';
        }

        return 'view.admin.ai.error_all_models_failed';
    }

    /**
     * @param  list<string>  $errors
     * @param  callable(string): bool  $predicate
     */
    private static function eachErrorMatches(array $errors, callable $predicate): bool
    {
        foreach ($errors as $error) {
            if (! $predicate($error)) {
                return false;
            }
        }

        return true;
    }

    private static function errorLooksLikeModelUnavailable(string $error): bool
    {
        return str_contains($error, 'not a valid model ID')
            || str_contains($error, 'No endpoints found')
            || str_contains($error, 'model_not_found')
            || str_contains($error, 'does not exist')
            || str_contains($error, 'HTTP 404');
    }
}
