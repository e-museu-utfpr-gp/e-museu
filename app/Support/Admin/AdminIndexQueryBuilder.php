<?php

namespace App\Support\Admin;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * Applies search and sort to an Eloquent query for admin index listings.
 *
 * @phpstan-type IndexConfig array{
 *   baseTable: string,
 *   searchBaseTable?: string,
 *   searchSpecial?: array<string, array{table: string, column: string}>,
 *   sortSpecial?: array<string, string>,
 *   booleanColumns?: array<int, string>,
 *   exactColumns?: array<int, string>
 * }
 */
class AdminIndexQueryBuilder
{
    /**
     * Apply search and sort from the request to the given query.
     *
     * @param  Builder<\Illuminate\Database\Eloquent\Model>  $query
     * @param  IndexConfig  $config
     */
    public static function build(Builder $query, Request $request, array $config): void
    {
        self::applySearchFilter($query, $request->search_column, $request->search, $config);
        self::applySort($query, $request->sort, $request->order, $config);
    }

    /**
     * @param  Builder<\Illuminate\Database\Eloquent\Model>  $query
     * @param  IndexConfig  $config
     * @param  string|int|bool|null  $search
     */
    private static function applySearchFilter(Builder $query, ?string $searchColumn, $search, array $config): void
    {
        if (! $searchColumn || $search === null || $search === '') {
            return;
        }

        $baseTable = $config['searchBaseTable'] ?? $config['baseTable'];
        $searchSpecialColumns = $config['searchSpecial'] ?? [];
        $booleanColumns = $config['booleanColumns'] ?? [];
        $exactColumns = $config['exactColumns'] ?? [];

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

        if (in_array($searchColumn, $exactColumns, true)) {
            $query->where("{$baseTable}.{$searchColumn}", '=', strtolower((string) $search));

            return;
        }

        $query->where("{$baseTable}.{$searchColumn}", 'LIKE', '%' . (string) $search . '%');
    }

    /**
     * @param  string|int|bool  $value
     */
    private static function normalizeSearchToBoolean($value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        return $value === 1 || $value === '1';
    }

    /**
     * @param  Builder<\Illuminate\Database\Eloquent\Model>  $query
     * @param  IndexConfig  $config
     */
    private static function applySort(Builder $query, ?string $sort, ?string $order, array $config): void
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
