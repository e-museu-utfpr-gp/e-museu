<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Admin\Auth\AdminLoginController;
use PHPUnit\Framework\TestCase;

/**
 * Unit contract for {@see AdminLoginController::username()} (distinct from Feature HTTP tests).
 */
class AdminLoginControllerUsernameFieldTest extends TestCase
{
    public function test_username_returns_username_field_name(): void
    {
        $controller = new AdminLoginController();

        $this->assertSame('username', $controller->username());
    }
}
