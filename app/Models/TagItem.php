<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TagItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'tag_id',
        'validation',
    ];

    protected $table = 'tag_item';

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function tag(): BelongsTo
    {
        return $this->belongsTo(Tag::class);
    }
}
