<?php

namespace App\Models\Identity;

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
        'user_id',
    ];

    protected $table = 'locks';

    public function lockable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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
}
