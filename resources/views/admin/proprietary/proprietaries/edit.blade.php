@extends('layouts.admin')
@section('title', __('view.admin.proprietary.proprietaries.edit.title', ['id' => $proprietary->id]))

@section('content')
    <div class="mb-auto container-fluid">
        @foreach ($errors->all() as $error)
            <div class="alert alert-danger" role="alert">
                {{ $error }}
            </div>
        @endforeach
        <form action="{{ route('admin.proprietaries.update', $proprietary->id) }}" method="POST">
            @csrf
            @method('PATCH')
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-3">
                        <h2 class="card-header">
                            {{ __('view.admin.proprietary.proprietaries.edit.heading', ['id' => $proprietary->id, 'name' => $proprietary->full_name]) }}
                        </h2>
                    </div>
                    <div class="mb-3">
                        <label for="full_name" class="form-label">
                            {{ __('view.admin.proprietary.proprietaries.edit.full_name') }}
                        </label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="full_name"
                            name="full_name" value="{{ $proprietary->full_name }}">
                        @error('full_name')
                            <div class="invalid-feedback"> {{ $message }} </div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="contact" class="form-label">
                            {{ __('view.admin.proprietary.proprietaries.edit.contact') }}
                        </label>
                        <input type="email" class="form-control @error('contact') is-invalid @enderror" id="contact"
                            name="contact" value="{{ $proprietary->contact }}">
                        @error('contact')
                            <div class="invalid-feedback"> {{ $message }} </div>
                        @enderror
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="mb-3">
                                <label for="blocked" class="form-label">
                                    {{ __('view.admin.proprietary.proprietaries.edit.blocked') }}
                                </label>
                                <select class="form-select @error('blocked') is-invalid @enderror" id="blocked" name="blocked">
                                    <option value="0" @if ($proprietary->blocked == 0) selected @endif>
                                        {{ __('view.admin.proprietary.proprietaries.edit.no') }}
                                    </option>
                                    <option value="1" @if ($proprietary->blocked == 1) selected @endif>
                                        {{ __('view.admin.proprietary.proprietaries.edit.yes') }}
                                    </option>
                                </select>
                                @error('blocked')
                                    <div class="invalid-feedback"> {{ $message }} </div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-3">
                                <label for="is_admin" class="form-label">
                                    {{ __('view.admin.proprietary.proprietaries.edit.is_admin') }}
                                </label>
                                <select class="form-select @error('is_admin') is-invalid @enderror" id="is_admin" name="is_admin">
                                    <option value="0" @if ($proprietary->is_admin == 0) selected @endif>
                                        {{ __('view.admin.proprietary.proprietaries.edit.no') }}
                                    </option>
                                    <option value="1" @if ($proprietary->is_admin == 1) selected @endif>
                                        {{ __('view.admin.proprietary.proprietaries.edit.yes') }}
                                    </option>
                                </select>
                                @error('is_admin')
                                    <div class="invalid-feedback"> {{ $message }} </div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-pencil-fill"></i>
                            {{ __('view.admin.proprietary.proprietaries.edit.submit') }}
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <x-release-lock-on-leave type="proprietaries" :id="$proprietary->id" />
@endsection
