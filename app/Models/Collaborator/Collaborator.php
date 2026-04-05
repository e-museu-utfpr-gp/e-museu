<?php

namespace App\Models\Collaborator;

use App\Enums\Collaborator\CollaboratorRole;
use App\Models\Identity\Lock;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Catalog\{Extra, Item};
use Illuminate\Database\Eloquent\Relations\{HasMany, MorphMany};

class Collaborator extends Model
{
    use HasFactory;

    protected $table = 'collaborators';

    protected $fillable = [
        'full_name',
        'email',
        'role',
        'blocked',
        'last_email_verification_at',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'role' => CollaboratorRole::class,
        'blocked' => 'boolean',
        'last_email_verification_at' => 'datetime',
    ];

    /**
     * Whether the collaborator has verified the e-mail with a code at least once (public catalog flow).
     */
    public function hasVerifiedEmail(): bool
    {
        return $this->last_email_verification_at !== null;
    }

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
