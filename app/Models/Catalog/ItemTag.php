<?php

namespace App\Models\Catalog;

use App\Models\Taxonomy\Tag;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemTag extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'tag_id',
        'validation',
    ];

    protected $table = 'item_tag';

    /**
     * Scope for admin index list: joins items and tags, selects columns with aliases for the index view.
     *
     * @param  Builder<ItemTag>  $query
     * @return Builder<ItemTag>
     */
    public function scopeForAdminList(Builder $query): Builder
    {
        $query->leftJoin('items', 'item_tag.item_id', '=', 'items.id')
            ->leftJoin('tags', 'item_tag.tag_id', '=', 'tags.id')
            ->select([
                'item_tag.id',
                'item_tag.item_id',
                'item_tag.tag_id',
                'item_tag.validation AS item_tag_validation',
                'item_tag.created_at AS item_tag_created',
                'item_tag.updated_at AS item_tag_updated',
                'items.name AS item_name',
                'tags.name AS tag_name',
            ]);

        return $query;
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function tag(): BelongsTo
    {
        return $this->belongsTo(Tag::class);
    }
}
