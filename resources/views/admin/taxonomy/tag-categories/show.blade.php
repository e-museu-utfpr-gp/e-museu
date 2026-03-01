@extends('layouts.admin')
@section('title', __('view.admin.taxonomy.tag_categories.show.title', ['id' => $tagCategory->id]))


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
                    <h2 class="card-header">
                        {{ __('view.admin.taxonomy.tag_categories.show.heading', ['id' => $tagCategory->id, 'name' => $tagCategory->name]) }}
                    </h2>
                    <div class="card-body d-flex">
                        <a href="{{ route('admin.tag-categories.edit', $tagCategory->id) }}" type="button"
                            class="btn btn-warning me-1">
                            <i class="bi bi-pencil-fill"></i>
                            {{ __('view.admin.taxonomy.tag_categories.show.edit') }}
                        </a>
                        <form action="{{ route('admin.tag-categories.destroy', $tagCategory->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="deleteCategoryButton btn btn-danger" data-confirm-message="{{ __('view.admin.taxonomy.tag_categories.delete_confirm') }}">
                                <i class="bi bi-trash-fill"></i>
                                {{ __('view.admin.taxonomy.tag_categories.show.delete') }}
                            </button>
                        </form>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <h5 class="card-header">
                                {{ __('view.admin.taxonomy.tag_categories.show.id') }}
                            </h5>
                            <div class="card-body">
                                <p class="card-text">{{ $tagCategory->id }}</p>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <h5 class="card-header">
                                {{ __('view.admin.taxonomy.tag_categories.show.name') }}
                            </h5>
                            <div class="card-body">
                                <p class="card-text">{{ $tagCategory->name }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <h5 class="card-header">
                                {{ __('view.admin.taxonomy.tag_categories.show.created_at') }}
                            </h5>
                            <div class="card-body">
                                <p class="ms-2">{{ date('d-m-Y H:i:s', strtotime($tagCategory->created_at)) }}</p>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <h5 class="card-header">
                                {{ __('view.admin.taxonomy.tag_categories.show.updated_at') }}
                            </h5>
                            <div class="card-body">
                                <p class="ms-2">{{ date('d-m-Y H:i:s', strtotime($tagCategory->updated_at)) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
