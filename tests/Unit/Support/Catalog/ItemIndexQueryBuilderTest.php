<?php

declare(strict_types=1);

namespace Tests\Unit\Support\Catalog;

use App\Models\Catalog\Item;
use App\Support\Catalog\ItemIndexQueryBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\Group;
use Tests\Unit\Services\ServiceMysqlTestCase;

#[Group('mysql')]
final class ItemIndexQueryBuilderTest extends ServiceMysqlTestCase
{
    public function test_build_returns_item_query_with_validation_scope(): void
    {
        $request = Request::create('/', 'GET');
        $query = ItemIndexQueryBuilder::build($request);

        $this->assertInstanceOf(Builder::class, $query);
        $this->assertInstanceOf(Item::class, $query->getModel());
        $sql = strtolower($query->toSql());
        $this->assertStringContainsString('where `validation`', $sql);
    }
}
