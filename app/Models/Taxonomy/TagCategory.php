<?php

namespace App\Models\Taxonomy;

use App\Models\Identity\Lock;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class TagCategory extends Model
{
    use HasFactory;

    protected $table = 'tag_categories';

    protected $fillable = [
        'name',
    ];

    public function tags(): HasMany
    {
        return $this->hasMany(Tag::class, 'tag_category_id')->orderBy('name', 'asc');
    }

    public function locks(): MorphMany
    {
        return $this->morphMany(Lock::class, 'lockable');
    }
}
