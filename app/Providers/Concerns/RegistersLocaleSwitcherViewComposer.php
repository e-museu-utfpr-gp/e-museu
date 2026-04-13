<?php

namespace App\Providers\Concerns;

use App\Models\Language;
use Illuminate\Support\Facades\View;

final class RegistersLocaleSwitcherViewComposer
{
    public static function register(): void
    {
        View::composer(
            ['components.layouts.admin', 'components.layouts.app'],
            function ($view): void {
                $view->with('localeSwitcherLanguages', Language::forLocaleSwitcher());
            }
        );
    }
}
