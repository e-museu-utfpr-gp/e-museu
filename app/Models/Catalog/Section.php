<?php

namespace App\Models\Catalog;

use App\Models\Identity\Lock;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Section extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    protected $table = 'sections';

    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }

    public function locks(): MorphMany
    {
        return $this->morphMany(Lock::class, 'lockable');
    }
}
