@extends('layouts.admin')
@section('title', __('view.admin.taxonomy.tag_categories.edit.title', ['id' => $tagCategory->id]))


@section('content')
    <div class="mb-auto container-fluid">
        @foreach ($errors->all() as $error)
            <div class="alert alert-danger" role="alert">
                {{ $error }}
            </div>
        @endforeach
        <form action="{{ route('admin.tag-categories.update', $tagCategory->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PATCH')
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-3">
                        <h2 class="card-header">
                            {{ __('view.admin.taxonomy.tag_categories.edit.heading', ['id' => $tagCategory->id, 'name' => $tagCategory->name]) }}
                        </h2>
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label">
                            {{ __('view.admin.taxonomy.tag_categories.edit.name') }}
                        </label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                            name="name" value="{{ $tagCategory->name }}">
                        @error('name')
                            <div class="invalid-feedback"> {{ $message }} </div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-pencil-fill"></i>
                            {{ __('view.admin.taxonomy.tag_categories.edit.submit') }}
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <x-release-lock-on-leave type="tag-categories" :id="$tagCategory->id" />
@endsection
