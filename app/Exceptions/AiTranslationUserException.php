<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;
use Throwable;

/**
 * User-facing AI translation failure; {@see $translationKey} is passed to the translator.
 */
final class AiTranslationUserException extends RuntimeException
{
    /**
     * @param  array<string, scalar>  $translationReplace  Passed to {@see __()} as replacements.
     */
    public function __construct(
        public readonly string $translationKey,
        public readonly array $translationReplace = [],
        ?Throwable $previous = null,
    ) {
        parent::__construct($translationKey, 0, $previous);
    }
}
