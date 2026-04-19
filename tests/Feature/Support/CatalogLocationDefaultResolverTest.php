<?php

namespace Tests\Feature\Support;

use App\Models\Location;
use App\Support\Catalog\CatalogLocationDefaultResolver;
use PHPUnit\Framework\Attributes\Group;
use Tests\Support\AbstractMysqlRefreshDatabaseTestCase;
use Tests\Support\Concerns\RequiresMysqlDriverConnection;

#[Group('mysql')]
class CatalogLocationDefaultResolverTest extends AbstractMysqlRefreshDatabaseTestCase
{
    use RequiresMysqlDriverConnection;

    public function test_default_location_id_matches_utfpr_code(): void
    {
        $id = CatalogLocationDefaultResolver::defaultLocationId();
        $this->assertNotNull($id);
        $row = Location::query()->findOrFail($id);
        $this->assertSame('UTFPR', $row->code);
    }

    public function test_default_falls_back_to_indef_when_utfpr_missing(): void
    {
        Location::query()->where('code', 'UTFPR')->delete();

        $id = CatalogLocationDefaultResolver::defaultLocationId();
        $this->assertNotNull($id);
        $row = Location::query()->findOrFail($id);
        $this->assertSame('INDEF', $row->code);
    }

    public function test_resolve_prefers_code_over_name_collision(): void
    {
        Location::factory()->withLocationCode('ZZZZZ')->create([
            'name' => 'UNCEN',
        ]);

        $uncenCampus = Location::query()->where('code', 'UNCEN')->firstOrFail();

        $this->assertSame(
            $uncenCampus->id,
            CatalogLocationDefaultResolver::resolveLocationId('UNCEN')
        );
    }

    public function test_resolve_falls_back_to_case_insensitive_name(): void
    {
        $unicentro = Location::query()->where('code', 'UNCEN')->firstOrFail();

        $this->assertSame($unicentro->id, CatalogLocationDefaultResolver::resolveLocationId('unicentro'));
    }

    public function test_default_returns_null_when_utfpr_and_indef_missing(): void
    {
        Location::query()->whereIn('code', ['UTFPR', 'INDEF'])->delete();

        $this->assertNull(CatalogLocationDefaultResolver::defaultLocationId());
    }
}
