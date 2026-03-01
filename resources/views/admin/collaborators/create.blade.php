@extends('layouts.admin')
@section('title', __('view.admin.collaborator.collaborators.create.title'))

@section('content')
    <div class="mb-auto container-fluid">
        @foreach ($errors->all() as $error)
            <div class="alert alert-danger" role="alert">
                {{ $error }}
            </div>
        @endforeach
        <form action="{{ route('admin.collaborators.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-3">
                        <h2 class="card-header">
                            {{ __('view.admin.collaborator.collaborators.create.heading') }}
                        </h2>
                    </div>
                    <div class="mb-3">
                        <label for="full_name" class="form-label">
                            {{ __('view.admin.collaborator.collaborators.create.full_name') }}
                        </label>
                        <input type="text" class="form-control @error('full_name') is-invalid @enderror" id="full_name"
                            name="full_name" value="{{ old('full_name') }}">
                        @error('full_name')
                            <div class="invalid-feedback"> {{ $message }} </div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="contact" class="form-label">
                            {{ __('view.admin.collaborator.collaborators.create.contact') }}
                        </label>
                        <input type="email" class="form-control @error('contact') is-invalid @enderror" id="contact"
                            name="contact" value="{{ old('contact') }}">
                        @error('contact')
                            <div class="invalid-feedback"> {{ $message }} </div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">
                            {{ __('view.admin.collaborator.collaborators.create.role') }}
                        </label>
                        <select class="form-select @error('role') is-invalid @enderror" id="role" name="role">
                            @php use App\Enums\CollaboratorRole; @endphp
                            <option value="{{ CollaboratorRole::EXTERNAL->value }}" {{ old('role', CollaboratorRole::EXTERNAL->value) === CollaboratorRole::EXTERNAL->value ? 'selected' : '' }}>
                                {{ __('app.collaborator.role.external') }}
                            </option>
                            <option value="{{ CollaboratorRole::INTERNAL->value }}" {{ old('role') === CollaboratorRole::INTERNAL->value ? 'selected' : '' }}>
                                {{ __('app.collaborator.role.internal') }}
                            </option>
                        </select>
                        @error('role')
                            <div class="invalid-feedback"> {{ $message }} </div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="blocked" class="form-label">
                            {{ __('view.admin.collaborator.collaborators.create.blocked') }}
                        </label>
                        <select class="form-select @error('blocked') is-invalid @enderror" id="blocked" name="blocked">
                            <option value="0" {{ old('blocked') == 0 ? 'selected' : '' }}>
                                {{ __('view.admin.collaborator.collaborators.create.no') }}
                            </option>
                            <option value="1" {{ old('blocked') == 1 ? 'selected' : '' }}>
                                {{ __('view.admin.collaborator.collaborators.create.yes') }}
                            </option>
                        </select>
                        @error('blocked')
                            <div class="invalid-feedback"> {{ $message }} </div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-plus-circle"></i>
                            {{ __('view.admin.collaborator.collaborators.create.submit') }}
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
