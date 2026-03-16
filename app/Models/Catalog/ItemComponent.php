<?php

namespace App\Models\Catalog;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemComponent extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'component_id',
        'validation',
    ];

    protected $table = 'item_component';

    /**
     * Scope for admin index list: joins items (item + component), selects columns with aliases for the index view.
     *
     * @param  Builder<ItemComponent>  $query
     * @return Builder<ItemComponent>
     */
    public function scopeForAdminList(Builder $query): Builder
    {
        $query->leftJoin('items as item', 'item_component.item_id', '=', 'item.id')
            ->leftJoin('items as component', 'item_component.component_id', '=', 'component.id')
            ->select([
                'item_component.id',
                'item_component.item_id',
                'item_component.component_id',
                'item_component.validation AS item_component_validation',
                'item_component.created_at AS item_component_created',
                'item_component.updated_at AS item_component_updated',
                'item.name AS item_name',
                'component.name AS component_name',
            ]);

        return $query;
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function component(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
