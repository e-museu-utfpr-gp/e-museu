@extends('layouts.admin')
@section('title', __('view.admin.catalog.extras.create.title'))

@section('content')
    <div class="mb-auto container-fluid">
        @foreach ($errors->all() as $error)
            <div class="alert alert-danger" role="alert">
                {{ $error }}
            </div>
        @endforeach
        <form action="{{ route('admin.extras.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-3">
                        <h2 class="card-header">{{ __('view.admin.catalog.extras.create.heading') }}</h2>
                    </div>
                    <div class="mb-3">
                        <label for="info" class="form-label">{{ __('view.admin.catalog.extras.create.info') }}</label>
                        <textarea type="text" class="form-control @error('info') is-invalid @enderror" id="info" name="info"
                            rows="5">{{ old('info') }}</textarea>
                        @error('info')
                            <div class="invalid-feedback"> {{ $message }} </div>
                        @enderror
                    </div>
                    <div class="row" data-section-item-selector 
                         data-section-selector="#section_id" 
                         data-item-selector="#item_id" 
                         data-original-item-id="{{ request()->query('id') }}"
                         data-get-items-url="{{ route('items.bySection') }}">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="section_id" class="form-label">{{ __('view.admin.catalog.extras.create.section') }}</label>
                                <select class="form-select @error('section_id') is-invalid @enderror" id="section_id"
                                    name="section_id">
                                    @foreach ($sections as $section)
                                        <option value="{{ $section->id }}"
                                            {{ old('section_id', request()->query('section')) == $section->id ? 'selected' : '' }}>
                                            {{ $section->name }}</option>
                                    @endforeach
                                </select>
                                @error('section_id')
                                    <div class="invalid-feedback"> {{ $message }} </div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="item_id" class="form-label">{{ __('view.admin.catalog.extras.create.item') }}</label>
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
                        <label for="proprietary_id" class="form-label">{{ __('view.admin.catalog.extras.create.collaborator') }}</label>
                        <select class="form-select @error('proprietary_id') is-invalid @enderror" id="proprietary_id"
                            name="proprietary_id">
                            @foreach ($proprietaries as $proprietary)
                                <option value="{{ $proprietary->id }}"
                                    {{ old('proprietary_id') == $proprietary->id ? 'selected' : '' }}>
                                    {{ $proprietary->contact }}</option>
                            @endforeach
                        </select>
                        @error('proprietary_id')
                            <div class="invalid-feedback"> {{ $message }} </div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="validation" class="form-label">{{ __('view.admin.catalog.extras.create.validation') }}</label>
                        <select class="form-select @error('validation') is-invalid @enderror" id="validation"
                            name="validation">
                            <option value="0" {{ old('validation') == 0 ? 'selected' : '' }}>{{ __('view.admin.catalog.extras.create.no') }}</option>
                            <option value="1" {{ old('validation') == 1 ? 'selected' : '' }}>{{ __('view.admin.catalog.extras.create.yes') }}</option>
                        </select>
                        @error('validation')
                            <div class="invalid-feedback"> {{ $message }} </div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <button type="submit" class="btn btn-success"><i class="bi bi-plus-circle"></i>
                            {{ __('view.admin.catalog.extras.create.submit') }}</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

@endsection
