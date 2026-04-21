<?php

declare(strict_types=1);

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Group;
use Tests\Support\AbstractMysqlRefreshDatabaseTestCase;

#[Group('mysql')]
class HomeControllerTest extends AbstractMysqlRefreshDatabaseTestCase
{
    public function test_home_page_renders_with_items_collection(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertViewIs('pages.home.index')
            ->assertViewHas('items');
    }
}
