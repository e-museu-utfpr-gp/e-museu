<?php

declare(strict_types=1);

namespace App\Providers\Concerns;

use Illuminate\Support\Facades\Session;

final class RegistersAdminDatabaseSessionHandler
{
    public static function register(): void
    {
        Session::extend('database', function ($app) {
            $table = $app['config']->get('session.table');
            $lifetime = $app['config']->get('session.lifetime');
            $connection = $app['config']->get('session.connection');
            $dbConnection = $app['db']->connection($connection);

            return new AdminDatabaseSessionHandler($dbConnection, $table, $lifetime, $app);
        });
    }
}
