<?php

namespace App\Providers\Concerns;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Session\DatabaseSessionHandler;

/**
 * Session handler que grava o id do autenticado na coluna admin_id (em vez de user_id).
 * Use com SESSION_DRIVER=database e tabela sessions contendo coluna admin_id.
 */
class AdminDatabaseSessionHandler extends DatabaseSessionHandler
{
    /**
     * Add the user information to the session payload.
     * Grava admin_id para refletir que o autenticado é um Admin.
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
