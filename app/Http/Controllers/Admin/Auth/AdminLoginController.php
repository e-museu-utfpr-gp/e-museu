<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Admin\AdminBaseController;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\View\View;

class AdminLoginController extends AdminBaseController
{
    use AuthenticatesUsers;

    protected string $redirectTo = '/admin/catalog/items';

    public function showLoginForm(): View
    {
        return view('pages.admin.auth.login');
    }

    /**
     * Field name used as login identifier by AuthenticatesUsers (default is 'email').
     * Returning 'username' makes admin login use username + password instead of email.
     */
    public function username(): string
    {
        return 'username';
    }
}
