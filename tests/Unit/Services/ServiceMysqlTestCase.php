<?php

namespace Tests\Unit\Services;

use PHPUnit\Framework\Attributes\Group;
use Tests\Support\AbstractMysqlRefreshDatabaseTestCase;

#[Group('mysql')]
#[Group('services')]
abstract class ServiceMysqlTestCase extends AbstractMysqlRefreshDatabaseTestCase
{
}
