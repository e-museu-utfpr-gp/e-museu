<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Location;
use App\Support\Catalog\CatalogLocationDefaultResolver;
use Illuminate\Database\Eloquent\Collection;

class LocationService
{
    /**
     * @return Collection<int, Location>
     */
    public function orderedForForms(): Collection
    {
        return Location::query()->orderBy('id')->get();
    }

    /**
     * One query for location selects plus in-memory default id (UTFPR → INDEF).
     *
     * @return array{locations: Collection<int, Location>, defaultCatalogLocationId: int|null}
     */
    public function forItemCreateForms(): array
    {
        $locations = $this->orderedForForms();

        return [
            'locations' => $locations,
            'defaultCatalogLocationId' => CatalogLocationDefaultResolver::defaultLocationIdFromCollection($locations),
        ];
    }
}
