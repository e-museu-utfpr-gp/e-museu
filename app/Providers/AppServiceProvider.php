<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\ServiceProvider;
use App\Providers\Concerns\{
    GuessesFactoryName,
    GuessesModelName,
    RegistersAdminDatabaseSessionHandler,
    RegistersAdminAiViewComposer,
    RegistersAdminLoginAntiBotViewComposer,
    RegistersAntiBotEmailCodeComposer,
    RegistersForcedHttpsUrls,
    RegistersLocaleSwitcherViewComposer,
    RegistersRateLimiters,
};

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RegistersForcedHttpsUrls::register();
        RegistersRateLimiters::register();
        RegistersAntiBotEmailCodeComposer::register();
        RegistersAdminLoginAntiBotViewComposer::register();

        Factory::guessFactoryNamesUsing([GuessesFactoryName::class, 'forModel']);
        Factory::guessModelNamesUsing([GuessesModelName::class, 'forFactory']);

        RegistersAdminDatabaseSessionHandler::register();
        RegistersLocaleSwitcherViewComposer::register();
        RegistersAdminAiViewComposer::register();
    }
}
