@extends('layouts.admin')
@section('title', __('view.admin.catalog.items.edit.title') . ' ' . $item->id)

@section('content')
    <div class="mb-auto container-fluid">
        @foreach ($errors->all() as $error)
            <div class="alert alert-danger" role="alert">
                {{ $error }}
            </div>
        @endforeach
        <form action="{{ route('admin.items.update', $item->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PATCH')
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-3">
                        <h2 class="card-header">{{ __('view.admin.catalog.items.edit.heading', ['id' => $item->id, 'name' => $item->name]) }}</h2>
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label">{{ __('view.admin.catalog.items.edit.name') }}</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                            name="name" value="{{ $item->name }}">
                        @error('name')
                            <div class="invalid-feedback"> {{ $message }} </div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">{{ __('view.admin.catalog.items.edit.description') }}</label>
                        <textarea type="text" class="form-control @error('description') is-invalid @enderror" id="description"
                            name="description" rows="5">{{ $item->description }}</textarea>
                        @error('description')
                            <div class="invalid-feedback"> {{ $message }} </div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="detail" class="form-label">{{ __('view.admin.catalog.items.edit.detail') }}</label>
                        <textarea type="text" class="form-control @error('detail') is-invalid @enderror" id="detail" name="detail"
                            rows="7">{{ $item->detail }}</textarea>
                        @error('detail')
                            <div class="invalid-feedback"> {{ $message }} </div>
                        @enderror
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="section_id" class="form-label">{{ __('view.admin.catalog.items.edit.section') }}</label>
                                <select class="form-select @error('section_id') is-invalid @enderror" id="section_id"
                                    name="section_id">
                                    @foreach ($sections as $section)
                                        <option value="{{ $section->id }}"
                                            @if ($item->section_id == $section->id) selected @endif>{{ $section->name }}</option>
                                    @endforeach
                                </select>
                                @error('section_id')
                                    <div class="invalid-feedback"> {{ $message }} </div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="proprietary_id" class="form-label">{{ __('view.admin.catalog.items.edit.collaborator') }}</label>
                                <select class="form-select @error('proprietary_id') is-invalid @enderror"
                                    id="proprietary_id" name="proprietary_id">
                                    @foreach ($proprietaries as $proprietary)
                                        <option value="{{ $proprietary->id }}"
                                            @if ($item->proprietary_id == $proprietary->id) selected @endif>{{ $proprietary->contact }} -
                                            {{ $proprietary->full_name }}</option>
                                    @endforeach
                                </select>
                                @error('proprietary_id')
                                    <div class="invalid-feedback"> {{ $message }} </div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="date" class="form-label">{{ __('view.admin.catalog.items.edit.date') }}</label>
                                <input type="date" class="form-control @error('date') is-invalid @enderror"
                                    id="date" name="date" value="{{ $item->date }}">
                                @error('date')
                                    <div class="invalid-feedback"> {{ $message }} </div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="identification_code" class="form-label">{{ __('view.admin.catalog.items.edit.identification_code') }}</label>
                                <input type="text"
                                    class="form-control @error('identification_code') is-invalid @enderror"
                                    id="identification_code" name="identification_code"
                                    value="{{ $item->identification_code }}">
                                @error('identification_code')
                                    <div class="invalid-feedback"> {{ $message }} </div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="validation" class="form-label">{{ __('view.admin.catalog.items.edit.validation') }}</label>
                                <select class="form-select @error('validation') is-invalid @enderror" id="validation"
                                    name="validation">
                                    <option value="0" @if ($item->validation == 0) selected @endif>{{ __('view.admin.catalog.items.edit.no') }}</option>
                                    <option value="1" @if ($item->validation == 1) selected @endif>{{ __('view.admin.catalog.items.edit.yes') }}</option>
                                </select>
                                @error('validation')
                                    <div class="invalid-feedback"> {{ $message }} </div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="image" class="form-label">{{ __('view.admin.catalog.items.edit.image') }}</label>
                                <input type="file" class="form-control @error('image') is-invalid @enderror"
                                    id="image" name="image">
                                @error('image')
                                    <div class="invalid-feedback"> {{ $message }} </div>
                                @enderror
                                @if ($item->image_url)
                                    <img src="{{ $item->image_url }}" class="img-thumbnail clickable-image"
                                        alt="{{ __('view.admin.catalog.items.edit.previous_image') }}">
                                    <p>{{ __('view.admin.catalog.items.edit.previous_image') }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="history" class="form-label">{{ __('view.admin.catalog.items.edit.history') }}</label>
                        <textarea type="text" class="form-control @error('history') is-invalid @enderror" id="history" name="history"
                            rows="46">{{ $item->history }}</textarea>
                        @error('history')
                            <div class="invalid-feedback"> {{ $message }} </div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <button type="submit" class="btn btn-warning"><i class="bi bi-pencil-fill"></i> {{ __('view.admin.catalog.items.edit.submit') }}</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @include('image-modal.img-modal')
    <x-release-lock-on-leave type="items" :id="$item->id" />
@endsection
