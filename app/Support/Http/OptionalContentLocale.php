<?php

declare(strict_types=1);

namespace App\Support\Http;

use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * Query/body param {@code content_locale}: empty/absent means "use site fallback";
 * non-empty must match a row in {@code languages.code}.
 */
final class OptionalContentLocale
{
    /**
     * @throws ValidationException
     */
    public static function languageIdOrNull(Request $request): ?int
    {
        $code = trim((string) ($request->input('content_locale') ?? ''));
        if ($code === '') {
            return null;
        }

        $id = Language::tryIdForCode($code);
        if ($id === null) {
            throw ValidationException::withMessages([
                'content_locale' => [__('validation.exists', ['attribute' => 'content_locale'])],
            ]);
        }

        return $id;
    }
}
