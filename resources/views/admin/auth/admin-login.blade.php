@extends('layouts.app')
@section('title', __('view.admin.auth.login.title'))

@section('content')
    <div class="container mb-auto mt-3">
        <h2>{{ __('view.admin.auth.login.heading') }}</h2>

        <div class="row">
            <div class="col-6">
                <form action="{{ route('login') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="exampleInputEmail1" class="form-label">{{ __('view.admin.auth.login.username_label') }}</label>
                        <input class="form-control" id="username" type="text" name="username" required autofocus>
                    </div>
                    <div class="mb-3">
                        <label for="exampleInputPassword1" class="form-label">{{ __('view.admin.auth.login.password_label') }}</label>
                        <input class="form-control" id="password" type="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary">{{ __('view.admin.auth.login.submit') }}</button>
                </form>
            </div>
        </div>
    </div>
@endsection
