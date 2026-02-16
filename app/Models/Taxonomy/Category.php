<?php

namespace App\Models\Taxonomy;

use App\Models\Identity\Lock;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    protected $table = 'categories';

    public function tags(): HasMany
    {
        return $this->hasMany(Tag::class)->orderBy('name', 'asc');
    }

    public function locks(): MorphMany
    {
        return $this->morphMany(Lock::class, 'lockable');
    }
}
