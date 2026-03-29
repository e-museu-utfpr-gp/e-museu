<?php

namespace App\Models\Catalog;

use App\Support\Content\TranslationDisplaySql;
use App\Models\Taxonomy\Tag;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

/**
 * Admin listings use {@see scopeForAdminList()} with names resolved via SQL ({@see TranslationDisplaySql});
 * keep this consistent with exports/views that rely on those columns.
 */
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
     * @param  Builder<ItemTag>  $query
     * @return Builder<ItemTag>
     */
    public function scopeForAdminList(Builder $query): Builder
    {
        $itemNameSql = TranslationDisplaySql::itemNameSubquerySql('items');
        $tagNameSql = TranslationDisplaySql::tagNameSubquerySql('tags');

        $query->leftJoin('items', 'item_tag.item_id', '=', 'items.id')
            ->leftJoin('tags', 'item_tag.tag_id', '=', 'tags.id')
            ->select([
                'item_tag.id',
                'item_tag.item_id',
                'item_tag.tag_id',
                'item_tag.validation AS item_tag_validation',
                'item_tag.created_at AS item_tag_created',
                'item_tag.updated_at AS item_tag_updated',
                DB::raw("({$itemNameSql}) AS item_name"),
                DB::raw("({$tagNameSql}) AS tag_name"),
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
