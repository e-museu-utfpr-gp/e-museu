<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;

/**
 * Applies search and sort to an Eloquent query for admin index listings.
 *
 * Config: baseTable, searchBaseTable (optional), searchSpecial, sortSpecial,
 * booleanColumns (optional): list of column names; search value must be 1/0 (from form).
 *
 * @phpstan-type IndexConfig array{
 *   baseTable: string,
 *   searchBaseTable?: string,
 *   searchSpecial?: array<string, array{table: string, column: string}>,
 *   sortSpecial?: array<string, string>,
 *   booleanColumns?: array<int, string>
 * }
 */
class AdminIndexQuery
{
    /**
     * @template T of \Illuminate\Database\Eloquent\Model
     * @param  Builder<T>  $query
     * @param  IndexConfig  $config
     * @param  string|int|bool|null  $search
     */
    public static function applySearch(Builder $query, ?string $searchColumn, $search, array $config): void
    {
        if (! $searchColumn || $search === null || $search === '') {
            return;
        }

        $baseTable = $config['searchBaseTable'] ?? $config['baseTable'];
        $searchSpecialColumns = $config['searchSpecial'] ?? [];
        $booleanColumns = $config['booleanColumns'] ?? [];

        if (isset($searchSpecialColumns[$searchColumn])) {
            $referencedTable = $searchSpecialColumns[$searchColumn]['table'];
            $referencedColumn = $searchSpecialColumns[$searchColumn]['column'];
            $query->where("{$referencedTable}.{$referencedColumn}", 'LIKE', '%' . (string) $search . '%');

            return;
        }

        if (in_array($searchColumn, $booleanColumns, true)) {
            $query->where("{$baseTable}.{$searchColumn}", self::normalizeSearchToBoolean($search));

            return;
        }

        $query->where("{$baseTable}.{$searchColumn}", 'LIKE', '%' . (string) $search . '%');
    }

    /**
     * @param  string|int|bool  $value  Expects 1/0 from the form for boolean columns.
     */
    private static function normalizeSearchToBoolean($value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        return $value === 1 || $value === '1';
    }

    /**
     * @template T of \Illuminate\Database\Eloquent\Model
     * @param  Builder<T>  $query
     * @param  IndexConfig  $config
     */
    public static function applySort(Builder $query, ?string $sort, ?string $order, array $config): void
    {
        if (! $sort || ! $order) {
            return;
        }

        $baseTable = $config['baseTable'];
        $sortSpecialColumns = $config['sortSpecial'] ?? [];
        $orderColumn = isset($sortSpecialColumns[$sort])
            ? $sortSpecialColumns[$sort]
            : "{$baseTable}.{$sort}";

        $query->orderBy($orderColumn, $order);
    }
}
