<?php

namespace App\Models;

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
        'category_id',
    ];

    protected $table = 'tags';

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'tag_item', 'tag_id', 'item_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function locks(): MorphMany
    {
        return $this->morphMany(Lock::class, 'lockable');
    }
}
