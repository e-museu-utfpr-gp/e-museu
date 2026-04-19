<?php

namespace Tests\Feature\Middleware;

use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

/**
 * Covers {@see \App\Http\Middleware\Auth\Authenticate} (alias {@code authenticate}).
 */
#[Group('middleware')]
class AuthenticateMiddlewareTest extends TestCase
{
    public function test_json_request_to_admin_without_session_returns_unauthorized(): void
    {
        $this->getJson(route('admin.catalog.items.index'))
            ->assertUnauthorized();
    }

    public function test_html_request_to_admin_without_session_redirects_to_login(): void
    {
        $this->get(route('admin.catalog.items.index'))
            ->assertRedirect(route('login'));
    }
}
