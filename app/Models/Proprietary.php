<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Proprietary extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'contact',
        'blocked',
        'is_admin',
    ];

    protected $table = 'proprietaries';

    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }

    public function locks(): MorphMany
    {
        return $this->morphMany(Lock::class, 'lockable');
    }
}
