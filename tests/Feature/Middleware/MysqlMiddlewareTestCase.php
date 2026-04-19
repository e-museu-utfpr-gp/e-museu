<?php

namespace Tests\Feature\Middleware;

use PHPUnit\Framework\Attributes\Group;
use Tests\Support\AbstractMysqlRefreshDatabaseTestCase;

/**
 * Feature middleware tests that need a working MySQL connection for
 * {@see \Illuminate\Foundation\Testing\RefreshDatabase}.
 */
#[Group('mysql')]
#[Group('middleware')]
abstract class MysqlMiddlewareTestCase extends AbstractMysqlRefreshDatabaseTestCase
{
}
