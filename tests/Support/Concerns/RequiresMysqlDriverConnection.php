<?php

namespace Tests\Support\Concerns;

use Illuminate\Support\Facades\DB;

/**
 * Use after {@see \Tests\Support\AbstractMysqlRefreshDatabaseTestCase} setup so migrations ran;
 * skips when the default connection is not MySQL (catalog translation SQL and some DDL are MySQL-specific).
 */
trait RequiresMysqlDriverConnection
{
    protected function setUp(): void
    {
        parent::setUp();

        if (DB::connection()->getDriverName() !== 'mysql') {
            $this->markTestSkipped(
                'Set DB_CONNECTION=mysql in .env.testing (catalog SQL and migrations assume MySQL).'
            );
        }
    }
}
