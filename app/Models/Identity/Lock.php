<?php

namespace App\Models\Identity;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Lock extends Model
{
    use HasFactory;

    protected $fillable = [
        'lockable_id',
        'lockable_type',
        'expiry_date',
        'admin_id',
    ];

    protected $table = 'locks';

    public function lockable(): MorphTo
    {
        return $this->morphTo();
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    /**
     * @return static|null
     *
     * @phpstan-return static|null
     */
    public static function findByModel(EloquentModel $subject)
    {
        /** @var static|null */
        $result = static::where('lockable_type', $subject::class)
            ->where('lockable_id', $subject->id)
            ->first();

        return $result;
    }

    public function expiresAt(): ?string
    {
        return $this->expiry_date;
    }

    /**
     * Whether another user's active lock blocks the given user from editing the model.
     * Expired locks are not considered active (and are removed).
     */
    public static function isLockedByOtherUser(Authenticatable $user, EloquentModel $subject): bool
    {
        $lock = static::where('lockable_type', $subject::class)
            ->where('lockable_id', $subject->getKey())
            ->first();

        if (! $lock || ! $lock->expiresAt()) {
            return false;
        }

        if (Carbon::parse($lock->expiry_date)->isPast()) {
            $lock->delete();

            return false;
        }

        return (string) $lock->admin_id !== (string) $user->getAuthIdentifier();
    }
}
