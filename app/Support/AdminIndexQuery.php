<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;

/**
 * Applies search and sort to an Eloquent query for admin index listings.
 *
 * Config: baseTable, searchBaseTable (optional, defaults to baseTable),
 * searchSpecial: [ 'col' => ['table'=>'...','column'=>'...'] ],
 * sortSpecial: [ 'col' => 'table.column' ].
 *
 * @phpstan-type IndexConfig array{
 *   baseTable: string,
 *   searchBaseTable?: string,
 *   searchSpecial?: array<string, array{table: string, column: string}>,
 *   sortSpecial?: array<string, string>
 * }
 */
class AdminIndexQuery
{
    /**
     * @template T of \Illuminate\Database\Eloquent\Model
     * @param  Builder<T>  $query
     * @param  IndexConfig  $config
     */
    public static function applySearch(Builder $query, ?string $searchColumn, ?string $search, array $config): void
    {
        if (! $searchColumn || ! $search) {
            return;
        }

        $baseTable = $config['searchBaseTable'] ?? $config['baseTable'];
        $searchSpecialColumns = $config['searchSpecial'] ?? [];

        if (isset($searchSpecialColumns[$searchColumn])) {
            $referencedTable = $searchSpecialColumns[$searchColumn]['table'];
            $referencedColumn = $searchSpecialColumns[$searchColumn]['column'];
            $query->where("{$referencedTable}.{$referencedColumn}", 'LIKE', "%{$search}%");

            return;
        }

        if ($search === 'sim') {
            $query->where("{$baseTable}.{$searchColumn}", true);

            return;
        }
        if ($search === 'não' || $search === 'nao') {
            $query->where("{$baseTable}.{$searchColumn}", false);

            return;
        }
        $query->where("{$baseTable}.{$searchColumn}", 'LIKE', "%{$search}%");
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
