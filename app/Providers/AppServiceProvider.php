<?php

namespace App\Providers;

use App\Providers\Concerns\GuessesFactoryName;
use App\Providers\Concerns\GuessesModelName;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Factory::guessFactoryNamesUsing([GuessesFactoryName::class, 'forModel']);
        Factory::guessModelNamesUsing([GuessesModelName::class, 'forFactory']);
    }
}
