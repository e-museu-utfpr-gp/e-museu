<?php

namespace App\Http\Controllers\Admin\Concerns;

use App\Models\Identity\Lock;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

trait LocksSubject
{
    /**
     * Ensure the subject is not locked by another user. Throws if it is.
     *
     * @throws AuthorizationException
     */
    public function requireUnlocked(Model $subject): void
    {
        $user = Auth::user();
        if ($user && Lock::isLockedByOtherUser($user, $subject)) {
            throw new AuthorizationException(__('app.identity.lock_blocked'));
        }
    }

    public function lock(Model $subject): Lock
    {
        $lock = Lock::findByModel($subject);
        $now = Carbon::now();
        $newExpiry = $now->copy()->addHours(1);

        if ($lock) {
            $isExpired = $lock->expiresAt() && Carbon::parse($lock->expiry_date)->isPast();
            $isOwnLock = (string) $lock->user_id === (string) Auth::id();

            if ($isExpired || ! $isOwnLock) {
                $lock->delete();
                $lock = null;
            } else {
                $lock->update(['expiry_date' => $newExpiry]);

                return $lock;
            }
        }

        Lock::where('user_id', Auth::id())->delete();
        $lock = $subject->locks()->create([
            'user_id' => Auth::id(),
            'expiry_date' => $newExpiry,
        ]);

        return $lock;
    }

    public static function unlock(Model $subject): bool
    {
        $lock = Lock::findByModel($subject);

        if ($lock) {
            $lock->delete();

            return true;
        }

        return false;
    }
}
