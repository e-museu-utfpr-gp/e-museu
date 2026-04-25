<?php

declare(strict_types=1);

namespace App\Support\Catalog;

use App\Models\Location;
use Illuminate\Database\Eloquent\Collection;
use RuntimeException;

/**
 * Resolves catalog {@see Location} rows for defaults and lookups. Prefer matching by {@see Location::$code}
 * (always stored uppercase); fall back to case-insensitive {@see Location::$name}.
 */
final class CatalogLocationDefaultResolver
{
    /** Preferred {@see Location::$code} for new contribution and admin create forms. */
    public const DEFAULT_CODE_OR_NAME = 'UTFPR';

    /** Used when the preferred default row is missing (soft fallback, still valid catalog semantics). */
    public const FALLBACK_CODE_OR_NAME = 'INDEF';

    /**
     * Default location id for forms: UTFPR if present, else INDEF. If neither exists, reports and returns null
     * (avoids hard-failing the application when reference data is incomplete).
     *
     * @internal Prefer {@see defaultLocationIdFromCollection()} with the same {@see Location} list used for
     *           the location select (one query, same ordering as the UI).
     *
     * @return int|null  Primary key, or null when no matching row and no safe default.
     */
    public static function defaultLocationId(): ?int
    {
        return self::defaultLocationIdFromCollection(Location::query()->orderBy('id')->get());
    }

    /**
     * Same resolution rules as {@see defaultLocationId()} using an already-loaded list (single-query forms).
     *
     * @param  Collection<int, Location>  $locations
     */
    public static function defaultLocationIdFromCollection(Collection $locations): ?int
    {
        foreach ([self::DEFAULT_CODE_OR_NAME, self::FALLBACK_CODE_OR_NAME] as $code) {
            $id = self::firstIdInCollectionByCode($locations, $code);
            if ($id !== null) {
                return $id;
            }
        }

        if ($locations->isEmpty()) {
            report(new RuntimeException('Catalog locations table is empty; cannot resolve default location.'));

            return null;
        }

        report(new RuntimeException(
            'Catalog default location missing: no row with code UTFPR or INDEF in `locations`.'
        ));

        return null;
    }

    /**
     * @param  Collection<int, Location>  $locations
     */
    private static function firstIdInCollectionByCode(Collection $locations, string $code): ?int
    {
        $upper = strtoupper(trim($code));
        foreach ($locations as $location) {
            if (strtoupper((string) $location->code) === $upper) {
                return (int) $location->id;
            }
        }

        return null;
    }

    /**
     * @return int|null  Location primary key, or null if not found.
     */
    public static function resolveLocationId(string $codeOrName): ?int
    {
        $trimmed = trim($codeOrName);
        if ($trimmed === '') {
            return null;
        }

        $byCode = Location::query()->where('code', strtoupper($trimmed))->value('id');
        if ($byCode !== null) {
            return (int) $byCode;
        }

        $byName = Location::query()
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($trimmed, 'UTF-8')])
            ->value('id');

        return $byName !== null ? (int) $byName : null;
    }
}
