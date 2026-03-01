@extends('layouts.admin')
@section('title', __('view.admin.taxonomy.tags.edit.title') . ' ' . $tag->id)

@section('content')
    <div class="mb-auto container-fluid">
        @foreach ($errors->all() as $error)
            <div class="alert alert-danger" role="alert">
                {{ $error }}
            </div>
        @endforeach
        <form action="{{ route('admin.tags.update', $tag->id) }}" method="POST">
            @csrf
            @method('PATCH')
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-3">
                        <h2 class="card-header">{{ __('view.admin.taxonomy.tags.edit.heading', ['id' => $tag->id, 'name' => $tag->name]) }}</h2>
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label">{{ __('view.admin.taxonomy.tags.edit.name') }}</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                            name="name" value="{{ $tag->name }}">
                        @error('name')
                            <div class="invalid-feedback"> {{ $message }} </div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="validation" class="form-label">{{ __('view.admin.taxonomy.tags.edit.validation') }}</label>
                        <select class="form-select @error('validation') is-invalid @enderror" id="validation"
                            name="validation">
                            <option value="0" @if ($tag->validation == 0) selected @endif>{{ __('view.admin.taxonomy.tags.edit.no') }}</option>
                            <option value="1" @if ($tag->validation == 1) selected @endif>{{ __('view.admin.taxonomy.tags.edit.yes') }}</option>
                        </select>
                        @error('validation')
                            <div class="invalid-feedback"> {{ $message }} </div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="category_id" class="form-label">{{ __('view.admin.taxonomy.tags.edit.category') }}</label>
                        <select class="form-select @error('category_id') is-invalid @enderror" id="category_id"
                            name="category_id">
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @if ($tag->category_id == $category->id) selected @endif>
                                    {{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('section_id')
                            <div class="invalid-feedback"> {{ $message }} </div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <button type="submit" class="btn btn-warning"><i class="bi bi-pencil-fill"></i> {{ __('view.admin.taxonomy.tags.edit.submit') }}</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <x-release-lock-on-leave type="tags" :id="$tag->id" />
@endsection
