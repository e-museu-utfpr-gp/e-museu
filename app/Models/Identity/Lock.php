<?php

declare(strict_types=1);

namespace App\Models\Identity;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{Model, Model as EloquentModel};
use Illuminate\Database\Eloquent\Relations\{BelongsTo, MorphTo};

class Lock extends Model
{
    use HasFactory;

    /**
     * In-memory cache for {@see findByModel()} within a single request so repeated lookups
     * (e.g. {@see \App\Services\Identity\LockService::requireUnlocked()} then lock acquisition)
     * do not hit the database twice. Invalidated when lock rows change.
     *
     * @var array<string, static|null>
     */
    private static array $findByModelCache = [];

    protected $fillable = [
        'lockable_id',
        'lockable_type',
        'expiry_date',
        'admin_id',
    ];

    protected $table = 'locks';

    protected static function booted(): void
    {
        static::saved(static function (Lock $lock): void {
            self::forgetFindByModelCacheEntry($lock->lockable_type, $lock->lockable_id);
        });

        static::deleted(static function (Lock $lock): void {
            self::forgetFindByModelCacheEntry($lock->lockable_type, $lock->lockable_id);
        });
    }

    public function lockable(): MorphTo
    {
        return $this->morphTo();
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    public static function findByModel(EloquentModel $subject): ?static
    {
        $key = self::findByModelCacheKey($subject);
        if (array_key_exists($key, self::$findByModelCache)) {
            return self::$findByModelCache[$key];
        }

        /** @var static|null */
        $result = static::query()
            ->where('lockable_type', $subject::class)
            ->where('lockable_id', $subject->getKey())
            ->first();

        self::$findByModelCache[$key] = $result;

        return $result;
    }

    /**
     * Clears the {@see findByModel()} cache. Needed after mass deletes, which do not fire model events.
     */
    public static function flushFindByModelCache(): void
    {
        self::$findByModelCache = [];
    }

    private static function findByModelCacheKey(EloquentModel $subject): string
    {
        return $subject::class . '::' . $subject->getKey();
    }

    private static function forgetFindByModelCacheEntry(?string $lockableType, mixed $lockableId): void
    {
        if ($lockableType === null || $lockableId === null) {
            return;
        }

        unset(self::$findByModelCache[$lockableType . '::' . $lockableId]);
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
        $lock = static::findByModel($subject);

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
