<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Admin\AdminBaseController;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminLoginController extends AdminBaseController
{
    use AuthenticatesUsers;

    protected string $redirectTo = '/admin';

    public function showLoginForm(): View
    {
        return view('admin.auth.admin-login');
    }

    public function username(): string
    {
        return 'username';
    }

    protected function validateLogin(Request $request): void
    {
        $request->validate([
            $this->username() => 'required|string',
            'password' => 'required|string',
        ]);
    }
}
