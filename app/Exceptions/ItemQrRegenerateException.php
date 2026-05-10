<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;
use Throwable;

/**
 * Controlled failure when (re)generating a catalog item QR image; carries a translation key for admin flash.
 */
final class ItemQrRegenerateException extends RuntimeException
{
    /**
     * @param  non-empty-string  $translationKey
     */
    public function __construct(
        public readonly string $translationKey,
        ?Throwable $previous = null,
    ) {
        parent::__construct($translationKey, 0, $previous);
    }
}
