@extends('layouts.admin')
@section('title', __('view.admin.catalog.sections.show.title', ['id' => $section->id]))

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
                        {{ __('view.admin.catalog.sections.show.heading', ['id' => $section->id, 'name' => $section->name]) }}
                    </h2>
                    <div class="card-body d-flex">
                        <a href="{{ route('admin.sections.edit', $section->id) }}" type="button"
                            class="btn btn-warning me-1">
                            <i class="bi bi-pencil-fill"></i>
                            {{ __('view.admin.catalog.sections.show.edit') }}
                        </a>
                        <form action="{{ route('admin.sections.destroy', $section->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="deleteSectionButton btn btn-danger" data-confirm-message="{{ __('view.admin.catalog.sections.delete_confirm') }}">
                                <i class="bi bi-trash-fill"></i>
                                {{ __('view.admin.catalog.sections.show.delete') }}
                            </button>
                        </form>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <h5 class="card-header">
                                {{ __('view.admin.catalog.sections.show.id') }}
                            </h5>
                            <div class="card-body">
                                <p class="card-text">{{ $section->id }}</p>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <h5 class="card-header">
                                {{ __('view.admin.catalog.sections.show.name') }}
                            </h5>
                            <div class="card-body">
                                <p class="card-text">{{ $section->name }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <h5 class="card-header">
                                {{ __('view.admin.catalog.sections.show.created_at') }}
                            </h5>
                            <div class="card-body">
                                <p class="ms-2">{{ date('d-m-Y H:i:s', strtotime($section->created_at)) }}</p>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <h5 class="card-header">
                                {{ __('view.admin.catalog.sections.show.updated_at') }}
                            </h5>
                            <div class="card-body">
                                <p class="ms-2">{{ date('d-m-Y H:i:s', strtotime($section->updated_at)) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
