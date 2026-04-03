<?php

namespace App\Services\Identity;

use App\Models\Identity\Lock;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class LockService
{
    /**
     * @param  array{type: string, id: int}  $data
     * @return array{0: EloquentModel|null, 1: int|null}
     */
    public function resolveSubject(array $data): array
    {
        $typeToRoutePrefix = [
            'items' => 'admin.catalog.items',
            'item-categories' => 'admin.catalog.item-categories',
            'tag-categories' => 'admin.taxonomy.tag-categories',
            'tags' => 'admin.taxonomy.tags',
            'collaborators' => 'admin.collaborators',
            'extras' => 'admin.catalog.extras',
        ];
        $routePrefix = $typeToRoutePrefix[$data['type']] ?? null;
        $config = config('lockable_routes', []);
        if ($routePrefix === null || ! isset($config[$routePrefix])) {
            return [null, Response::HTTP_BAD_REQUEST];
        }

        [, $modelClass] = $config[$routePrefix];
        $subject = $modelClass::find((int) $data['id']);
        if (! $subject) {
            return [null, Response::HTTP_NOT_FOUND];
        }

        return [$subject, null];
    }

    public function releaseLockForCurrentAdmin(EloquentModel $subject): void
    {
        $lock = Lock::findByModel($subject);
        if ($lock && (string) $lock->admin_id === (string) Auth::id()) {
            $lock->delete();
        }
    }

    public function isLockedByOtherUser(EloquentModel $subject): bool
    {
        $user = Auth::user();

        if (! $user) {
            return false;
        }

        return Lock::isLockedByOtherUser($user, $subject);
    }

    /**
     * @throws AuthorizationException
     */
    public function requireUnlocked(EloquentModel $subject): void
    {
        if ($this->isLockedByOtherUser($subject)) {
            throw new AuthorizationException(__('app.identity.lock_blocked'));
        }
    }

    /**
     * Ensures the subject is not locked by another admin, then creates or extends the current admin's lock.
     * Uses one lock row lookup (avoids duplicate query vs {@see requireUnlocked()} then {@see lock()}).
     *
     * @throws AuthorizationException
     */
    public function requireUnlockedThenLock(EloquentModel $subject): Lock
    {
        $user = Auth::user();
        if ($user === null) {
            throw new AuthorizationException(__('app.identity.lock_blocked'));
        }

        $lock = Lock::findByModel($subject);
        $userId = (string) $user->getAuthIdentifier();

        if ($lock && $lock->expiresAt()) {
            if (Carbon::parse($lock->expiry_date)->isPast()) {
                $lock->delete();
                $lock = null;
            } elseif ((string) $lock->admin_id !== $userId) {
                throw new AuthorizationException(__('app.identity.lock_blocked'));
            }
        }

        return $this->materializeLock($subject, $lock, $userId);
    }

    public function lock(EloquentModel $subject): Lock
    {
        return $this->materializeLock($subject, Lock::findByModel($subject), (string) Auth::id());
    }

    /**
     * Creates a new lock or extends an existing one for the current admin (subject must already be editable).
     */
    private function materializeLock(EloquentModel $subject, ?Lock $lock, string $userId): Lock
    {
        $newExpiry = Carbon::now()->addHour();

        if ($lock) {
            $isExpired = $lock->expiresAt() && Carbon::parse($lock->expiry_date)->isPast();
            $isOwnLock = (string) $lock->admin_id === $userId;
            if ($isExpired || ! $isOwnLock) {
                $lock->delete();
                $lock = null;
            } else {
                $lock->update(['expiry_date' => $newExpiry]);

                return $lock;
            }
        }

        Lock::where('admin_id', $userId)->delete();
        Lock::flushFindByModelCache();

        /** @var Lock $created */
        $created = $subject->locks()->create([
            'admin_id' => $userId,
            'expiry_date' => $newExpiry,
        ]);

        return $created;
    }

    public function unlock(EloquentModel $subject): bool
    {
        $lock = Lock::findByModel($subject);

        if ($lock) {
            $lock->delete();

            return true;
        }

        return false;
    }
}
