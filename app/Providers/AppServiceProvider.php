<?php

namespace App\Providers;

use App\Models\Language;
use App\Providers\Concerns\AdminDatabaseSessionHandler;
use App\Providers\Concerns\GuessesFactoryName;
use App\Providers\Concerns\GuessesModelName;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
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

        Session::extend('database', function ($app) {
            $table = $app['config']->get('session.table');
            $lifetime = $app['config']->get('session.lifetime');
            $connection = $app['config']->get('session.connection');
            $dbConnection = $app['db']->connection($connection);

            return new AdminDatabaseSessionHandler($dbConnection, $table, $lifetime, $app);
        });

        View::composer(
            ['components.layouts.admin', 'components.layouts.app'],
            function ($view): void {
                $view->with('localeSwitcherLanguages', Language::forLocaleSwitcher());
            }
        );
    }
}
