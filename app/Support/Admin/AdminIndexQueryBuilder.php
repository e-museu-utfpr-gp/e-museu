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
 *   searchLikeSubquery?: array<string, string>,
 *   sortSubquery?: array<string, string>,
 *   sortSpecial?: array<string, string>,
 *   booleanColumns?: array<int, string>,
 *   exactColumns?: array<int, string>,
 *   exactColumnsNumeric?: array<int, string>
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
     * @param  string|int|bool|null  $search
     * @param  IndexConfig  $config
     */
    private static function applySearchFilter(Builder $query, ?string $searchColumn, $search, array $config): void
    {
        if (! $searchColumn || $search === null || $search === '') {
            return;
        }

        $baseTable = $config['searchBaseTable'] ?? $config['baseTable'];

        if (
            self::applySearchSpecialJoinColumn($query, $searchColumn, $search, $config)
            || self::applySearchLikeSubquery($query, $searchColumn, $search, $config)
            || self::applySearchBoolean($query, $searchColumn, $search, $config, $baseTable)
            || self::applySearchExactNumeric($query, $searchColumn, $search, $config, $baseTable)
            || self::applySearchExactLowercase($query, $searchColumn, $search, $config, $baseTable)
        ) {
            return;
        }

        $query->where("{$baseTable}.{$searchColumn}", 'LIKE', '%' . (string) $search . '%');
    }

    /**
     * @param  Builder<\Illuminate\Database\Eloquent\Model>  $query
     * @param  string|int|bool  $search
     * @param  IndexConfig  $config
     */
    private static function applySearchSpecialJoinColumn(
        Builder $query,
        string $searchColumn,
        $search,
        array $config,
    ): bool {
        $searchSpecialColumns = $config['searchSpecial'] ?? [];
        if (! isset($searchSpecialColumns[$searchColumn])) {
            return false;
        }

        $referencedTable = $searchSpecialColumns[$searchColumn]['table'];
        $referencedColumn = $searchSpecialColumns[$searchColumn]['column'];
        $query->where("{$referencedTable}.{$referencedColumn}", 'LIKE', '%' . (string) $search . '%');

        return true;
    }

    /**
     * @param  Builder<\Illuminate\Database\Eloquent\Model>  $query
     * @param  string|int|bool  $search
     * @param  IndexConfig  $config
     */
    private static function applySearchLikeSubquery(Builder $query, string $searchColumn, $search, array $config): bool
    {
        $searchLikeSubquery = $config['searchLikeSubquery'] ?? [];
        if (! isset($searchLikeSubquery[$searchColumn])) {
            return false;
        }

        $sql = $searchLikeSubquery[$searchColumn];
        $query->whereRaw("{$sql} LIKE ?", ['%' . (string) $search . '%']);

        return true;
    }

    /**
     * @param  Builder<\Illuminate\Database\Eloquent\Model>  $query
     * @param  string|int|bool  $search
     * @param  IndexConfig  $config
     */
    private static function applySearchBoolean(
        Builder $query,
        string $searchColumn,
        $search,
        array $config,
        string $baseTable,
    ): bool {
        $booleanColumns = $config['booleanColumns'] ?? [];
        if (! in_array($searchColumn, $booleanColumns, true)) {
            return false;
        }

        $query->where("{$baseTable}.{$searchColumn}", self::normalizeSearchToBoolean($search));

        return true;
    }

    /**
     * @param  Builder<\Illuminate\Database\Eloquent\Model>  $query
     * @param  string|int|bool  $search
     * @param  IndexConfig  $config
     */
    private static function applySearchExactNumeric(
        Builder $query,
        string $searchColumn,
        $search,
        array $config,
        string $baseTable,
    ): bool {
        $exactColumnsNumeric = $config['exactColumnsNumeric'] ?? [];
        if (! in_array($searchColumn, $exactColumnsNumeric, true)) {
            return false;
        }

        $raw = is_string($search) ? trim($search) : (string) $search;
        if ($raw === '' || ! ctype_digit($raw)) {
            $query->whereRaw('0 = 1');

            return true;
        }

        $query->where("{$baseTable}.{$searchColumn}", '=', (int) $raw);

        return true;
    }

    /**
     * @param  Builder<\Illuminate\Database\Eloquent\Model>  $query
     * @param  string|int|bool  $search
     * @param  IndexConfig  $config
     */
    private static function applySearchExactLowercase(
        Builder $query,
        string $searchColumn,
        $search,
        array $config,
        string $baseTable,
    ): bool {
        $exactColumns = $config['exactColumns'] ?? [];
        if (! in_array($searchColumn, $exactColumns, true)) {
            return false;
        }

        $query->where("{$baseTable}.{$searchColumn}", '=', strtolower((string) $search));

        return true;
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
        $sortSubquery = $config['sortSubquery'] ?? [];
        if (isset($sortSubquery[$sort])) {
            $dir = strtolower((string) $order) === 'desc' ? 'desc' : 'asc';
            $query->orderByRaw("{$sortSubquery[$sort]} {$dir}");

            return;
        }

        $sortSpecialColumns = $config['sortSpecial'] ?? [];
        $orderColumn = isset($sortSpecialColumns[$sort])
            ? $sortSpecialColumns[$sort]
            : "{$baseTable}.{$sort}";

        $query->orderBy($orderColumn, $order);
    }
}
