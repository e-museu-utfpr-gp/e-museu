@extends('layouts.admin')
@section('title', __('view.admin.proprietary.proprietaries.create.title'))

@section('content')
    <div class="mb-auto container-fluid">
        @foreach ($errors->all() as $error)
            <div class="alert alert-danger" role="alert">
                {{ $error }}
            </div>
        @endforeach
        <form action="{{ route('admin.proprietaries.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-3">
                        <h2 class="card-header">
                            {{ __('view.admin.proprietary.proprietaries.create.heading') }}
                        </h2>
                    </div>
                    <div class="mb-3">
                        <label for="full_name" class="form-label">
                            {{ __('view.admin.proprietary.proprietaries.create.full_name') }}
                        </label>
                        <input type="text" class="form-control @error('full_name') is-invalid @enderror" id="full_name"
                            name="full_name" value="{{ old('full_name') }}">
                        @error('full_name')
                            <div class="invalid-feedback"> {{ $message }} </div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="contact" class="form-label">
                            {{ __('view.admin.proprietary.proprietaries.create.contact') }}
                        </label>
                        <input type="email" class="form-control @error('contact') is-invalid @enderror" id="contact"
                            name="contact" value="{{ old('contact') }}">
                        @error('contact')
                            <div class="invalid-feedback"> {{ $message }} </div>
                        @enderror
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="mb-3">
                                <label for="blocked" class="form-label">
                                    {{ __('view.admin.proprietary.proprietaries.create.blocked') }}
                                </label>
                                <select class="form-select @error('blocked') is-invalid @enderror" id="blocked" name="blocked">
                                    <option value="0" {{ old('blocked') == 0 ? 'selected' : '' }}>
                                        {{ __('view.admin.proprietary.proprietaries.create.no') }}
                                    </option>
                                    <option value="1" {{ old('blocked') == 1 ? 'selected' : '' }}>
                                        {{ __('view.admin.proprietary.proprietaries.create.yes') }}
                                    </option>
                                </select>
                                @error('validation')
                                    <div class="invalid-feedback"> {{ $message }} </div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-3">
                                <label for="is_admin" class="form-label">
                                    {{ __('view.admin.proprietary.proprietaries.create.is_admin') }}
                                </label>
                                <select class="form-select @error('is_admin') is-invalid @enderror" id="is_admin" name="is_admin">
                                    <option value="0" {{ old('is_admin') == 0 ? 'selected' : '' }}>
                                        {{ __('view.admin.proprietary.proprietaries.create.no') }}
                                    </option>
                                    <option value="1" {{ old('is_admin') == 1 ? 'selected' : '' }}>
                                        {{ __('view.admin.proprietary.proprietaries.create.yes') }}
                                    </option>
                                </select>
                                @error('validation')
                                    <div class="invalid-feedback"> {{ $message }} </div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-plus-circle"></i>
                            {{ __('view.admin.proprietary.proprietaries.create.submit') }}
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
