<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Extra extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'proprietary_id',
        'info',
        'validation',
    ];

    protected $table = 'extras';

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function proprietary(): BelongsTo
    {
        return $this->belongsTo(Proprietary::class);
    }

    public function locks(): MorphMany
    {
        return $this->morphMany(Lock::class, 'lockable');
    }
}
