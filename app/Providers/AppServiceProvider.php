<?php

namespace App\Providers;

use App\Models\Language;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{RateLimiter, Session, View};
use Illuminate\Support\ServiceProvider;
use App\Providers\Concerns\{AdminDatabaseSessionHandler, GuessesFactoryName, GuessesModelName};

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
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('web-public', function (Request $request) {
            return Limit::perMinute(120)->by($request->ip());
        });

        /** Generous limit for catalog autocomplete / name-check JSON (shared IPs, many quick requests). */
        RateLimiter::for('web-catalog-light', function (Request $request) {
            return Limit::perMinute(400)->by($request->ip());
        });

        RateLimiter::for('web-admin', function (Request $request) {
            return Limit::perMinute(480)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('web-storage', function (Request $request) {
            return Limit::perMinute(2000)->by($request->ip());
        });

        RateLimiter::for('admin-login', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip());
        });

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
