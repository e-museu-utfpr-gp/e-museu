<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Proprietary\Proprietary;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */
    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     */
    protected string $redirectTo = '/';

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array<string, mixed>  $data
     */
    protected function validator(array $data): ValidatorContract
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:proprietaries,contact'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array<string, mixed>  $data
     */
    protected function create(array $data): Proprietary
    {
        return Proprietary::create([
            'full_name' => $data['name'],
            'contact' => $data['email'],
            'is_admin' => true,
        ]);
    }
}
