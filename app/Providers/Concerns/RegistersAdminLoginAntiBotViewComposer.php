<?php

namespace App\Providers\Concerns;

use App\Support\Security\AntiBotVerifier;
use Illuminate\Support\Facades\View;

final class RegistersAdminLoginAntiBotViewComposer
{
    public static function register(): void
    {
        View::composer('pages.admin.auth.login', function ($view): void {
            $verifier = app(AntiBotVerifier::class);
            if (! $verifier->isActive()) {
                $view->with('adminLoginAntiBotTurnstileData', null);

                return;
            }

            $view->with('adminLoginAntiBotTurnstileData', $verifier->challengeViewData());
        });
    }
}
