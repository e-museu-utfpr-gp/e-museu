@extends('layouts.admin')
@section('title', __('view.admin.collaborator.collaborators.edit.title', ['id' => $collaborator->id]))

@section('content')
    <div class="mb-auto container-fluid">
        @foreach ($errors->all() as $error)
            <div class="alert alert-danger" role="alert">
                {{ $error }}
            </div>
        @endforeach
        <form action="{{ route('admin.collaborators.update', $collaborator->id) }}" method="POST">
            @csrf
            @method('PATCH')
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-3">
                        <h2 class="card-header">
                            {{ __('view.admin.collaborator.collaborators.edit.heading', ['id' => $collaborator->id, 'name' => $collaborator->full_name]) }}
                        </h2>
                    </div>
                    <div class="mb-3">
                        <label for="full_name" class="form-label">
                            {{ __('view.admin.collaborator.collaborators.edit.full_name') }}
                        </label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="full_name"
                            name="full_name" value="{{ $collaborator->full_name }}">
                        @error('full_name')
                            <div class="invalid-feedback"> {{ $message }} </div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="contact" class="form-label">
                            {{ __('view.admin.collaborator.collaborators.edit.contact') }}
                        </label>
                        <input type="email" class="form-control @error('contact') is-invalid @enderror" id="contact"
                            name="contact" value="{{ $collaborator->contact }}">
                        @error('contact')
                            <div class="invalid-feedback"> {{ $message }} </div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">
                            {{ __('view.admin.collaborator.collaborators.edit.role') }}
                        </label>
                        <select class="form-select @error('role') is-invalid @enderror" id="role" name="role">
                            @php
                                use App\Enums\CollaboratorRole;
                                $roleValue = optional($collaborator->role)?->value ?? CollaboratorRole::EXTERNAL->value;
                            @endphp
                            <option value="{{ CollaboratorRole::EXTERNAL->value }}" @if ($roleValue === CollaboratorRole::EXTERNAL->value) selected @endif>
                                {{ __('app.collaborator.role.external') }}
                            </option>
                            <option value="{{ CollaboratorRole::INTERNAL->value }}" @if ($roleValue === CollaboratorRole::INTERNAL->value) selected @endif>
                                {{ __('app.collaborator.role.internal') }}
                            </option>
                        </select>
                        @error('role')
                            <div class="invalid-feedback"> {{ $message }} </div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="blocked" class="form-label">
                            {{ __('view.admin.collaborator.collaborators.edit.blocked') }}
                        </label>
                        <select class="form-select @error('blocked') is-invalid @enderror" id="blocked" name="blocked">
                            <option value="0" @if ($collaborator->blocked == 0) selected @endif>
                                {{ __('view.admin.collaborator.collaborators.edit.no') }}
                            </option>
                            <option value="1" @if ($collaborator->blocked == 1) selected @endif>
                                {{ __('view.admin.collaborator.collaborators.edit.yes') }}
                            </option>
                        </select>
                        @error('blocked')
                            <div class="invalid-feedback"> {{ $message }} </div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-pencil-fill"></i>
                            {{ __('view.admin.collaborator.collaborators.edit.submit') }}
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <x-release-lock-on-leave type="collaborators" :id="$collaborator->id" />
@endsection
