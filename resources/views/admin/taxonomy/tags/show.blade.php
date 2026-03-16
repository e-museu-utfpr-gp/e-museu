@extends('layouts.admin')
@section('title', __('view.admin.taxonomy.tags.show.title') . ' ' . $tag->id)

@section('content')
    <div class="mb-auto container-fluid">
        @if (session('success'))
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
        @endif
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-3">
                    <h2 class="card-header">{{ __('view.admin.taxonomy.tags.show.heading', ['id' => $tag->id, 'name' => $tag->name]) }}</h2>
                    <div class="card-body d-flex">
                        <a href="{{ route('admin.tags.edit', $tag->id) }}" type="button" class="btn btn-warning me-1"><i
                                class="bi bi-pencil-fill"></i> {{ __('view.admin.taxonomy.tags.show.edit') }}</a>
                        <form action="{{ route('admin.tags.destroy', $tag->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="deleteTagButton btn btn-danger" data-confirm-message="{{ __('view.admin.taxonomy.tags.index.delete_confirm') }}"><i class="bi bi-trash-fill"></i>
                                {{ __('view.admin.taxonomy.tags.show.delete') }}
                        </form>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <h5 class="card-header">{{ __('view.admin.taxonomy.tags.show.id') }}</h5>
                            <div class="card-body">
                                <p class="card-text">{{ $tag->id }}</p>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <h5 class="card-header">{{ __('view.admin.taxonomy.tags.show.name') }}</h5>
                            <div class="card-body">
                                <p class="card-text">{{ $tag->name }}</p>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <h5 class="card-header">{{ __('view.admin.taxonomy.tags.show.validated') }}</h5>
                            <div class="card-body">
                                <p class="card-text">
                                    @if ($tag->validation == 1)
                                        {{ __('view.admin.taxonomy.tags.index.yes') }}
                                    @else
                                        {{ __('view.admin.taxonomy.tags.index.no') }}
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <h5 class="card-header">{{ __('view.admin.taxonomy.tags.show.created_at') }}</h5>
                            <div class="card-body">
                                <p class="ms-2">{{ date('d-m-Y H:i:s', strtotime($tag->created_at)) }}</p>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <h5 class="card-header">{{ __('view.admin.taxonomy.tags.show.updated_at') }}</h5>
                            <div class="card-body">
                                <p class="ms-2">{{ date('d-m-Y H:i:s', strtotime($tag->updated_at)) }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <h5 class="card-header">{{ __('view.admin.taxonomy.tags.show.category') }}</h5>
                            <div class="card-body">
                                <strong>{{ __('view.admin.taxonomy.tags.show.id') }}: </strong>
                                <p class="ms-3">{{ $tag->tagCategory->id }}</p>
                                <strong>{{ __('view.admin.taxonomy.tags.show.name') }}: </strong>
                                <p class="card-text">{{ $tag->tagCategory->name }}</p>
                                <strong>{{ __('view.admin.taxonomy.tags.show.created_at') }}: </strong>
                                <p class="ms-2">{{ date('d-m-Y H:i:s', strtotime($tag->tagCategory->created_at)) }}</p>
                                <strong>{{ __('view.admin.taxonomy.tags.show.updated_at') }}: </strong>
                                <p class="ms-2">{{ date('d-m-Y H:i:s', strtotime($tag->tagCategory->updated_at)) }}</p>
                                <div class="d-flex">
                                    <a href="{{ route('admin.tag-categories.show', $tag->tagCategory->id) }}" type="button"
                                        class="btn btn-primary me-1"><i class="bi bi-eye-fill"></i> {{ __('view.admin.taxonomy.tags.show.view') }}</a>
                                    <a href="{{ route('admin.tag-categories.edit', $tag->tagCategory->id) }}" type="button"
                                        class="btn btn-warning me-1"><i class="bi bi-pencil-fill"></i> {{ __('view.admin.taxonomy.tags.show.edit') }}</a>
                                    <form action="{{ route('admin.tag-categories.destroy', $tag->tagCategory->id) }}"
                                        method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button class="deleteCategoryButton btn btn-danger" type="submit" data-confirm-message="{{ __('view.admin.taxonomy.tag_categories.delete_confirm') }}"><i
                                                class="bi bi-trash-fill"></i> {{ __('view.admin.taxonomy.tags.show.delete') }}
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
