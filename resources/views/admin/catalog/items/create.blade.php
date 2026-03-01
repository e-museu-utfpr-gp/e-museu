@extends('layouts.admin')
@section('title', __('view.admin.catalog.items.create.title'))

@section('content')
    <div class="mb-auto container-fluid">
        @foreach ($errors->all() as $error)
            <div class="alert alert-danger" role="alert">
                {{ $error }}
            </div>
        @endforeach
        <form action="{{ route('admin.items.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-3">
                        <h2 class="card-header">{{ __('view.admin.catalog.items.create.heading') }}</h2>
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label">{{ __('view.admin.catalog.items.create.name') }}</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                            name="name" value="{{ old('name') }}">
                        @error('name')
                            <div class="invalid-feedback"> {{ $message }} </div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">{{ __('view.admin.catalog.items.create.description') }}</label>
                        <textarea type="text" class="form-control @error('description') is-invalid @enderror" id="description"
                            name="description" rows="5">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback"> {{ $message }} </div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="detail" class="form-label">{{ __('view.admin.catalog.items.create.detail') }}</label>
                        <textarea type="text" class="form-control @error('detail') is-invalid @enderror" id="detail" name="detail"
                            rows="7">{{ old('detail') }}</textarea>
                        @error('detail')
                            <div class="invalid-feedback"> {{ $message }} </div>
                        @enderror
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="category_id" class="form-label">{{ __('view.admin.catalog.items.create.item_category') }}</label>
                                <select class="form-select @error('category_id') is-invalid @enderror" id="category_id"
                                    name="category_id">
                                    <option selected="selected" value="">-</option>
                                    @foreach ($sections as $section)
                                        <option value="{{ $section->id }}"
                                            {{ old('category_id') == $section->id ? 'selected' : '' }}>{{ $section->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback"> {{ $message }} </div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="collaborator_id" class="form-label">{{ __('view.admin.catalog.items.create.collaborator') }}</label>
                                <select class="form-select @error('collaborator_id') is-invalid @enderror"
                                    id="collaborator_id" name="collaborator_id">
                                    <option selected="selected" value="">-</option>
                                    @foreach ($collaborators as $collaborator)
                                        <option value="{{ $collaborator->id }}"
                                            {{ old('collaborator_id') == $collaborator->id ? 'selected' : '' }}>
                                            {{ $collaborator->contact }} - {{ $collaborator->full_name }}</option>
                                    @endforeach
                                </select>
                                @error('collaborator_id')
                                    <div class="invalid-feedback"> {{ $message }} </div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="date" class="form-label">{{ __('view.admin.catalog.items.create.date') }}</label>
                                <input type="date" class="form-control @error('date') is-invalid @enderror"
                                    id="date" name="date" value="{{ old('date') }}">
                                @error('date')
                                    <div class="invalid-feedback"> {{ $message }} </div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="image" class="form-label">{{ __('view.admin.catalog.items.create.image') }}</label>
                                <input type="file" class="form-control @error('image') is-invalid @enderror"
                                    id="image" name="image">
                                @error('image')
                                    <div class="invalid-feedback"> {{ $message }} </div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="validation" class="form-label">{{ __('view.admin.catalog.items.create.validation') }}</label>
                                <select class="form-select @error('validation') is-invalid @enderror" id="validation"
                                    name="validation">
                                    <option value="0" {{ old('validation') == 0 ? 'selected' : '' }}>{{ __('view.admin.catalog.items.create.no') }}</option>
                                    <option value="1" {{ old('validation') == 1 ? 'selected' : '' }}>{{ __('view.admin.catalog.items.create.yes') }}</option>
                                </select>
                                @error('validation')
                                    <div class="invalid-feedback"> {{ $message }} </div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="history" class="form-label">{{ __('view.admin.catalog.items.create.history') }}</label>
                        <textarea type="text" class="form-control @error('history') is-invalid @enderror" id="history" name="history"
                            rows="46">{{ old('history') }}</textarea>
                        @error('history')
                            <div class="invalid-feedback"> {{ $message }} </div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <button type="submit" class="btn btn-success"><i class="bi bi-plus-circle"></i> {{ __('view.admin.catalog.items.create.submit') }}</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
