@extends('layouts.admin')
@section('title', __('view.admin.catalog.item_categories.edit.title', ['id' => $itemCategory->id]))

@section('content')
    <div class="mb-auto container-fluid">
        @foreach ($errors->all() as $error)
            <div class="alert alert-danger" role="alert">
                {{ $error }}
            </div>
        @endforeach
        <form action="{{ route('admin.item-categories.update', $itemCategory->id) }}" method="POST">
            @csrf
            @method('PATCH')
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-3">
                        <h2 class="card-header">
                            {{ __('view.admin.catalog.item_categories.edit.heading', ['id' => $itemCategory->id, 'name' => $itemCategory->name]) }}
                        </h2>
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label">
                            {{ __('view.admin.catalog.item_categories.edit.name') }}
                        </label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                            name="name" value="{{ $itemCategory->name }}">
                        @error('name')
                            <div class="invalid-feedback"> {{ $message }} </div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-pencil-fill"></i>
                            {{ __('view.admin.catalog.item_categories.edit.submit') }}
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <x-release-lock-on-leave type="item-categories" :id="$itemCategory->id" />
@endsection
