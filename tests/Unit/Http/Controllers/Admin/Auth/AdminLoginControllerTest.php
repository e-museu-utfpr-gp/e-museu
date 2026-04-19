<?php

namespace Tests\Unit\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Admin\Auth\AdminLoginController;
use PHPUnit\Framework\TestCase;

class AdminLoginControllerTest extends TestCase
{
    public function test_username_returns_username_field_name(): void
    {
        $controller = new AdminLoginController();

        $this->assertSame('username', $controller->username());
    }
}
