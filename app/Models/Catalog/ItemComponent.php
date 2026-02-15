<?php

namespace App\Models\Catalog;

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

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function component(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
