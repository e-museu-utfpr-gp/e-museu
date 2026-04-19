<?php

namespace Tests\Unit\Support\Catalog;

use App\Models\Location;
use App\Support\Catalog\CatalogLocationDefaultResolver;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Tests\TestCase;

final class CatalogLocationDefaultResolverCollectionTest extends TestCase
{
    /**
     * Resolver compares {@see Location::$code} case-insensitively (uppercased), so mixed casing here is intentional.
     */
    public function test_prefers_utfpr_over_indef(): void
    {
        $indef = (new Location())->forceFill(['id' => 2, 'code' => 'INDEF', 'name' => 'Indef']);
        $utfpr = (new Location())->forceFill(['id' => 1, 'code' => 'utfpr', 'name' => 'UTFPR']);

        $id = CatalogLocationDefaultResolver::defaultLocationIdFromCollection(
            new EloquentCollection([$indef, $utfpr])
        );

        $this->assertSame(1, $id);
    }

    public function test_falls_back_to_indef_when_utfpr_missing(): void
    {
        $indef = (new Location())->forceFill(['id' => 7, 'code' => 'indef', 'name' => 'Indef']);

        $id = CatalogLocationDefaultResolver::defaultLocationIdFromCollection(
            new EloquentCollection([$indef])
        );

        $this->assertSame(7, $id);
    }

    public function test_returns_null_when_collection_empty(): void
    {
        $id = CatalogLocationDefaultResolver::defaultLocationIdFromCollection(new EloquentCollection());

        $this->assertNull($id);
    }

    public function test_returns_null_when_no_default_codes_present(): void
    {
        $other = (new Location())->forceFill(['id' => 9, 'code' => 'OTHER', 'name' => 'Other']);

        $id = CatalogLocationDefaultResolver::defaultLocationIdFromCollection(
            new EloquentCollection([$other])
        );

        $this->assertNull($id);
    }
}
