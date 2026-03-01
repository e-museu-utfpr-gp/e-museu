<?php

namespace App\Models\Collaborator;

use App\Enums\CollaboratorRole;
use App\Models\Catalog\Extra;
use App\Models\Catalog\Item;
use App\Models\Identity\Lock;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Collaborator extends Model
{
    use HasFactory;

    protected $table = 'collaborators';

    protected $fillable = [
        'full_name',
        'contact',
        'role',
        'blocked',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'role' => CollaboratorRole::class,
    ];

    public function items(): HasMany
    {
        return $this->hasMany(Item::class, 'collaborator_id');
    }

    public function extras(): HasMany
    {
        return $this->hasMany(Extra::class, 'collaborator_id');
    }

    public function locks(): MorphMany
    {
        return $this->morphMany(Lock::class, 'lockable');
    }
}
