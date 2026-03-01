@extends('layouts.app')
@section('title', __('view.auth.verify.title'))

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('view.auth.verify.card_header') }}</div>

                    <div class="card-body">
                        @if (session('resent'))
                            <div class="alert alert-success" role="alert">
                                {{ __('view.auth.verify.resent') }}
                            </div>
                        @endif

                        {{ __('view.auth.verify.before_proceeding') }}
                        {{ __('view.auth.verify.if_not_received') }},
                        <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                            @csrf
                            <button type="submit"
                                class="btn btn-link p-0 m-0 align-baseline">{{ __('view.auth.verify.click_here') }}</button>.
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
