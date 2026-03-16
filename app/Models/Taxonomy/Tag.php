<?php

namespace App\Models\Taxonomy;

use App\Models\Catalog\Item;
use App\Models\Identity\Lock;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'validation',
        'tag_category_id',
    ];

    protected $table = 'tags';

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'item_tag', 'tag_id', 'item_id');
    }

    public function tagCategory(): BelongsTo
    {
        return $this->belongsTo(TagCategory::class, 'tag_category_id');
    }

    public function locks(): MorphMany
    {
        return $this->morphMany(Lock::class, 'lockable');
    }
}
