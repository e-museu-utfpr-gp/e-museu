<?php

declare(strict_types=1);

namespace App\Support\Database;

use Illuminate\Contracts\Database\Query\Expression as ExpressionContract;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;

/**
 * Wraps dynamic SQL fragments for the query builder. {@see DB::raw()} and {@see Builder::whereRaw()}
 * stubs require {@code literal-string}; these call sites build SQL only from validated identifiers
 * and bound parameters (never raw user text).
 *
 * **When to use:** Prefer normal Eloquent / the query builder (`where`, `whereIn`, `orderBy`, …).
 * Use this class only when the SQL string is **built at runtime** (concatenation, interpolation,
 * or helpers like {@see \App\Support\Content\TranslationDisplaySql}) and PHPStan reports
 * `literal-string` / `argument.type` on {@see DB::raw()}, `selectRaw`, `whereRaw`, `orderByRaw`,
 * or `orWhereRaw`. Run static analysis after adding raw SQL; if it stays clean, do not introduce
 * `SqlExpr` here.
 *
 * **Do not use for:** Untrusted input stitched into SQL (use bindings and the regular API instead).
 */
final class SqlExpr
{
    /**
     * @param non-falsy-string $sql
     */
    public static function raw(string $sql): ExpressionContract
    {
        /** @phpstan-ignore argument.type */
        return DB::raw($sql);
    }

    /**
     * @template TModel of Model
     *
     * @param  EloquentBuilder<TModel>|QueryBuilder  $query
     * @param  non-falsy-string  $sql
     * @param  array<int, mixed>  $bindings
     */
    public static function selectRaw(EloquentBuilder|QueryBuilder $query, string $sql, array $bindings = []): mixed
    {
        /** @phpstan-ignore argument.type */
        return $query->selectRaw($sql, $bindings);
    }

    /**
     * @template TModel of Model
     *
     * @param  EloquentBuilder<TModel>|QueryBuilder  $query
     * @param  non-falsy-string  $sql
     * @param  array<int, mixed>  $bindings
     */
    public static function orderByRaw(EloquentBuilder|QueryBuilder $query, string $sql, array $bindings = []): mixed
    {
        /** @phpstan-ignore argument.type */
        return $query->orderByRaw($sql, $bindings);
    }

    /**
     * @template TModel of Model
     *
     * @param  EloquentBuilder<TModel>|QueryBuilder  $query
     * @param  non-falsy-string  $sql
     * @param  array<int, mixed>  $bindings
     */
    public static function whereRaw(
        EloquentBuilder|QueryBuilder $query,
        string $sql,
        array $bindings = [],
        string $boolean = 'and',
    ): mixed {
        /** @phpstan-ignore argument.type */
        return $query->whereRaw($sql, $bindings, $boolean);
    }

    /**
     * @template TModel of Model
     *
     * @param  EloquentBuilder<TModel>|QueryBuilder  $query
     * @param  non-falsy-string  $sql
     * @param  array<int, mixed>  $bindings
     */
    public static function orWhereRaw(EloquentBuilder|QueryBuilder $query, string $sql, array $bindings = []): mixed
    {
        /** @phpstan-ignore argument.type */
        return $query->orWhereRaw($sql, $bindings);
    }

    /**
     * @template T of Relation
     *
     * @param  T  $relation
     * @param  non-falsy-string  $sql
     * @param  array<int, mixed>  $bindings
     * @return T
     */
    public static function relationWhereRaw(Relation $relation, string $sql, array $bindings = []): Relation
    {
        /** @phpstan-ignore argument.type */
        return $relation->whereRaw($sql, $bindings);
    }
}
