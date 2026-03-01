@extends('layouts.admin')
@section('title', __('view.admin.catalog.extras.edit.title') . ' ' . $extra->id)

@section('content')
    <div class="mb-auto container-fluid">
        @foreach ($errors->all() as $error)
            <div class="alert alert-danger" role="alert">
                {{ $error }}
            </div>
        @endforeach
        <form action="{{ route('admin.extras.update', $extra->id) }}" method="POST">
            @csrf
            @method('PATCH')
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-3">
                        <h2 class="card-header">{{ __('view.admin.catalog.extras.edit.heading', ['id' => $extra->id]) }}</h2>
                    </div>
                    <div class="mb-3">
                        <label for="info" class="form-label">{{ __('view.admin.catalog.extras.edit.info') }}</label>
                        <textarea type="text" class="form-control @error('info') is-invalid @enderror" id="info" name="info"
                            rows="5">{{ $extra->info }}</textarea>
                        @error('info')
                            <div class="invalid-feedback"> {{ $message }} </div>
                        @enderror
                    </div>
                    <div class="row" data-section-item-selector 
                         data-section-selector="#category_id" 
                         data-item-selector="#item_id" 
                         data-original-item-id="{{ $extra->item->id }}"
                         data-get-items-url="{{ route('items.bySection') }}">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="category_id" class="form-label">{{ __('view.admin.catalog.extras.edit.item_category') }}</label>
                                <select class="form-select @error('category_id') is-invalid @enderror" id="category_id"
                                    name="category_id">
                                    @foreach ($sections as $section)
                                        <option value="{{ $section->id }}"
                                            {{ $extra->item->category?->id == $section->id ? 'selected' : '' }}>
                                            {{ $section->name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback"> {{ $message }} </div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="item_id" class="form-label">{{ __('view.admin.catalog.extras.edit.item') }}</label>
                                <select class="form-select @error('item_id') is-invalid @enderror" id="item_id"
                                    name="item_id">
                                </select>
                                @error('item_id')
                                    <div class="invalid-feedback"> {{ $message }} </div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="collaborator_id" class="form-label">{{ __('view.admin.catalog.extras.edit.collaborator') }}</label>
                        <select class="form-select @error('collaborator_id') is-invalid @enderror" id="collaborator_id"
                            name="collaborator_id">
                            @foreach ($collaborators as $collaborator)
                                <option value="{{ $collaborator->id }}"
                                    {{ $extra->collaborator->id == $collaborator->id ? 'selected' : '' }}>
                                    {{ $collaborator->contact }}</option>
                            @endforeach
                        </select>
                        @error('collaborator_id')
                            <div class="invalid-feedback"> {{ $message }} </div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="validation" class="form-label">{{ __('view.admin.catalog.extras.edit.validation') }}</label>
                        <select class="form-select @error('validation') is-invalid @enderror" id="validation"
                            name="validation">
                            <option value="0" {{ $extra->validation == 0 ? 'selected' : '' }}>{{ __('view.admin.catalog.extras.edit.no') }}</option>
                            <option value="1" {{ $extra->validation == 1 ? 'selected' : '' }}>{{ __('view.admin.catalog.extras.edit.yes') }}</option>
                        </select>
                        @error('validation')
                            <div class="invalid-feedback"> {{ $message }} </div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <button type="submit" class="btn btn-warning"><i class="bi bi-pencil-fill"></i> {{ __('view.admin.catalog.extras.edit.submit') }}</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <x-release-lock-on-leave type="extras" :id="$extra->id" />
@endsection
