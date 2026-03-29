<?php

namespace App\Providers\Concerns;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Session\DatabaseSessionHandler;

/**
 * Session handler that persists the authenticated principal's id in the admin_id column (instead of user_id).
 * Use with SESSION_DRIVER=database and a sessions table that includes admin_id.
 */
class AdminDatabaseSessionHandler extends DatabaseSessionHandler
{
    /**
     * Add the user information to the session payload.
     * Writes admin_id to reflect that the authenticated user is an Admin.
     *
     * @param  array<string, mixed>  $payload
     * @return $this
     */
    protected function addUserInformation(&$payload)
    {
        if ($this->container !== null && $this->container->bound(Guard::class)) {
            $payload['admin_id'] = $this->userId();
        }

        return $this;
    }
}
