<?php

declare(strict_types=1);

namespace App\Providers\Concerns;

use App\Support\Security\AntiBotVerifier;
use Illuminate\Support\Facades\View;

final class RegistersAntiBotEmailCodeComposer
{
    public static function register(): void
    {
        View::composer('pages.catalog.items._partials.email-verification-code', function ($view): void {
            $verifier = app(AntiBotVerifier::class);
            if (! $verifier->isActive()) {
                $view->with('antiBotTurnstileWidgetData', null);

                return;
            }

            $field = (string) config('antibot.verification_request_response_input');
            $view->with('antiBotTurnstileWidgetData', array_merge($verifier->challengeViewData(), [
                'responseFieldName' => $field,
            ]));
        });
    }
}
