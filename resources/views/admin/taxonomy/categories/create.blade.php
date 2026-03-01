@extends('layouts.admin')
@section('title', __('view.admin.taxonomy.categories.create.title'))

@section('content')
    <div class="mb-auto container-fluid">
        @foreach ($errors->all() as $error)
            <div class="alert alert-danger" role="alert">
                {{ $error }}
            </div>
        @endforeach
        <form action="{{ route('admin.categories.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-3">
                        <h2 class="card-header">
                            {{ __('view.admin.taxonomy.categories.create.heading') }}
                        </h2>
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label">
                            {{ __('view.admin.taxonomy.categories.create.name') }}
                        </label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                            name="name" value="{{ old('name') }}">
                        @error('name')
                            <div class="invalid-feedback"> {{ $message }} </div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-plus-circle"></i>
                            {{ __('view.admin.taxonomy.categories.create.submit') }}
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
