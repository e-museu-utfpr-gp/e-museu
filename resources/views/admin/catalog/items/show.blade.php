@extends('layouts.admin')
@section('title', __('view.admin.catalog.items.show.title') . ' ' . $item->id)

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
                    <h2 class="card-header">{{ __('view.admin.catalog.items.show.heading', ['id' => $item->id, 'name' => $item->name]) }}</h2>
                    <div class="card-body d-flex">
                        <a href="{{ route('admin.items.edit', $item->id) }}" type="button" class="btn btn-warning me-1"><i
                                class="bi bi-pencil-fill"></i> {{ __('view.admin.catalog.items.show.edit') }}</a>
                        <form action="{{ route('admin.items.destroy', $item->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button id="deleteItemButton" type="submit" class="btn btn-danger deleteItemButton" data-confirm-message="{{ __('view.admin.catalog.items.show.delete_confirm') }}"><i
                                    class="bi bi-trash-fill"></i> {{ __('view.admin.catalog.items.show.delete') }}
                        </form>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <h5 class="card-header">{{ __('view.admin.catalog.items.show.id') }}</h5>
                            <div class="card-body">
                                <p class="card-text">{{ $item->id }}</p>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <h5 class="card-header">{{ __('view.admin.catalog.items.show.name') }}</h5>
                            <div class="card-body">
                                <p class="card-text">{{ $item->name }}</p>
                            </div>
                        </div>
                        @if ($item->image_url)
                            <div class="card mb-3">
                                <h5 class="card-header">{{ __('view.admin.catalog.items.show.image') }}</h5>
                                <div class="card-body">
                                    <img src="{{ $item->image_url }}" class="img-thumbnail clickable-image myImg"
                                        alt="{{ __('view.admin.catalog.items.show.image') }}">
                                </div>
                            </div>
                        @endif
                        <div class="card mb-3">
                            <h5 class="card-header">{{ __('view.admin.catalog.items.show.description') }}</h5>
                            <div class="card-body">
                                <p class="card-text">{{ $item->description }}</p>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <h5 class="card-header">{{ __('view.admin.catalog.items.show.detail') }}</h5>
                            <div class="card-body">
                                <p class="card-text">{!! nl2br($item->detail) !!}</p>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <h5 class="card-header">{{ __('view.admin.catalog.items.show.date') }}</h5>
                            <div class="card-body">
                                <p class="card-text">{{ date('d-m-Y', strtotime($item->date)) }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <h5 class="card-header">{{ __('view.admin.catalog.items.show.identification_code') }}</h5>
                            <div class="card-body">
                                <p class="card-text">{{ $item->identification_code }}</p>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <h5 class="card-header">{{ __('view.admin.catalog.items.show.validated') }}</h5>
                            <div class="card-body">
                                <p class="card-text">
                                    @if ($item->validation == 1)
                                        {{ __('view.admin.catalog.items.show.yes') }}
                                    @else
                                        {{ __('view.admin.catalog.items.show.no') }}
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <h5 class="card-header">{{ __('view.admin.catalog.items.show.created_at') }}</h5>
                            <div class="card-body">
                                <p class="ms-2">{{ date('d-m-Y H:i:s', strtotime($item->created_at)) }}</p>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <h5 class="card-header">{{ __('view.admin.catalog.items.show.updated_at') }}</h5>
                            <div class="card-body">
                                <p class="ms-2">{{ date('d-m-Y H:i:s', strtotime($item->updated_at)) }}</p>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <h5 class="card-header">{{ __('view.admin.catalog.items.show.section') }}</h5>
                            <div class="card-body">
                                <strong>{{ __('view.admin.catalog.items.show.id') }}: </strong>
                                <p class="ms-3">{{ $item->section->id }}</p>
                                <strong>{{ __('view.admin.catalog.items.show.name') }}: </strong>
                                <p class="card-text">{{ $item->section->name }}</p>
                                <strong>{{ __('view.admin.catalog.items.show.created_at') }}: </strong>
                                <p class="ms-2">{{ date('d-m-Y', strtotime($item->created_at)) }}</p>
                                <strong>{{ __('view.admin.catalog.items.show.updated_at') }}: </strong>
                                <p class="ms-2">{{ date('d-m-Y', strtotime($item->updated_at)) }}</p>
                                <div class="d-flex">
                                    <a href="{{ route('admin.sections.show', $item->section->id) }}" type="button"
                                        class="btn btn-primary me-1"><i class="bi bi-eye-fill"></i> {{ __('view.admin.catalog.items.show.view') }}</a>
                                    <a href="{{ route('admin.sections.edit', $item->section->id) }}" type="button"
                                        class="btn btn-warning me-1"><i class="bi bi-pencil-fill"></i> {{ __('view.admin.catalog.items.show.edit') }}</a>
                                    <form action="{{ route('admin.sections.destroy', $item->section->id) }}"
                                        method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button id="deleteSectionButton" class="deleteSectionButton btn btn-danger"
                                            type="submit" data-confirm-message="{{ __('view.admin.catalog.sections.delete_confirm') }}"><i class="bi bi-trash-fill"></i> {{ __('view.admin.catalog.items.show.delete') }}
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <h5 class="card-header">{{ __('view.admin.catalog.items.show.collaborator') }}</h5>
                            <div class="card-body">
                                <strong>{{ __('view.admin.catalog.items.show.id') }}: </strong>
                                <p class="ms-3">{{ $item->proprietary->id }}</p>
                                <strong>{{ __('view.admin.catalog.items.show.full_name') }}: </strong>
                                <p class="ms-3">{{ $item->proprietary->full_name }}</p>
                                <strong>{{ __('view.admin.catalog.items.show.contact') }}: </strong>
                                <p class="ms-3">{{ $item->proprietary->contact }}</p>
                                <strong>{{ __('view.admin.catalog.items.show.blocked') }}: </strong>
                                <p class="ms-3">
                                    @if ($item->proprietary->blocked == 1)
                                        {{ __('view.admin.catalog.items.show.yes') }}
                                    @else
                                        {{ __('view.admin.catalog.items.show.no') }}
                                    @endif
                                </p>
                                <strong>{{ __('view.admin.catalog.items.show.created_at') }}: </strong>
                                <p class="ms-3">{{ date('d-m-Y', strtotime($item->proprietary->created_at)) }}</p>
                                <strong>{{ __('view.admin.catalog.items.show.updated_at') }}: </strong>
                                <p class="ms-3">{{ date('d-m-Y', strtotime($item->proprietary->updated_at)) }}</p>
                                <div class="d-flex">
                                    <a href="{{ route('admin.proprietaries.show', $item->proprietary->id) }}"
                                        type="button" class="btn btn-primary me-1"><i class="bi bi-eye-fill"></i>
                                        {{ __('view.admin.catalog.items.show.view') }}</a>
                                    <a href="{{ route('admin.proprietaries.edit', $item->proprietary->id) }}"
                                        type="button" class="btn btn-warning me-1"><i class="bi bi-pencil-fill"></i>
                                        {{ __('view.admin.catalog.items.show.edit') }}</a>
                                    <form action="{{ route('admin.proprietaries.destroy', $item->proprietary->id) }}"
                                        method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button id="deleteProprietaryButton"
                                            class="deleteProprietaryButton btn btn-danger" type="submit"><i
                                                class="bi bi-trash-fill"></i> {{ __('view.admin.catalog.items.show.delete') }}
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card mb-3">
                    <h5 class="card-header">{{ __('view.admin.catalog.items.show.history') }}</h5>
                    <div class="card-body">
                        <p>{{ $item->history }}</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="card mb-3">
                            <h5 class="card-header d-flex justify-content-between">{{ __('view.admin.catalog.items.show.extra_info') }} <a type="button"
                                    class="btn btn-success"
                                    href="{{ route('admin.extras.create', ['id' => $item->id, 'section' => $item->section]) }}"><i
                                        class="bi bi-plus-circle"></i> {{ __('view.admin.catalog.items.show.add_extra') }}</a></h5>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    @foreach ($item->extras as $extra)
                                        <li class="list-group-item">
                                            <strong>{{ __('view.admin.catalog.items.show.id') }}: </strong>
                                            <p class="ms-3">{{ $extra->id }}</p>
                                            <strong>{{ __('view.admin.catalog.items.show.curiosity') }}: </strong>
                                            <p class="ms-3">{{ Str::limit($extra->info, 500) }}</p>
                                            <strong>{{ __('view.admin.catalog.items.show.validated_label') }}: </strong>
                                            <p class="ms-3">
                                                @if ($extra->validation == 1)
                                                    {{ __('view.admin.catalog.items.show.yes') }}
                                                @else
                                                    {{ __('view.admin.catalog.items.show.no') }}
                                                @endif
                                            </p>
                                            <strong>{{ __('view.admin.catalog.items.show.item_label') }}: </strong>
                                            <p class="ms-3">{{ $extra->item->name }}</p>
                                            <strong>{{ __('view.admin.catalog.items.show.collaborator') }}: </strong>
                                            <p class="ms-3">{{ $extra->proprietary->full_name }}</p>
                                            <strong>{{ __('view.admin.catalog.items.show.created_at') }}: </strong>
                                            <p class="ms-3">{{ date('d-m-Y', strtotime($extra->created_at)) }}</p>
                                            <strong>{{ __('view.admin.catalog.items.show.updated_at') }}: </strong>
                                            <p class="ms-3">{{ date('d-m-Y', strtotime($extra->updated_at)) }}</p>
                                            <div class="d-flex">
                                                <a href="{{ route('admin.extras.show', $extra->id) }}" type="button"
                                                    class="btn btn-primary me-1"><i class="bi bi-eye-fill"></i>
                                                    {{ __('view.admin.catalog.items.show.view') }}</a>
                                                <a href="{{ route('admin.extras.edit', $extra->id) }}" type="button"
                                                    class="btn btn-warning me-1"><i class="bi bi-pencil-fill"></i>
                                                    {{ __('view.admin.catalog.items.show.edit') }}</a>
                                                <form action="{{ route('admin.extras.destroy', $extra->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="deleteExtraButton btn btn-danger" type="submit" data-confirm-message="{{ __('view.admin.catalog.extras.index.delete_confirm') }}"><i
                                                            class="bi bi-trash-fill"></i> {{ __('view.admin.catalog.items.show.delete') }}
                                                </form>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <h5 class="card-header d-flex justify-content-between">{{ __('view.admin.catalog.items.show.related_tags') }}<a type="button"
                                    class="btn btn-success"
                                    href="{{ route('admin.item-tags.create', ['id' => $item->id, 'section' => $item->section]) }}"><i
                                        class="bi bi-plus-circle"></i> {{ __('view.admin.catalog.items.show.add_tag') }}</a></h5>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    @foreach ($item->tagItems as $tagItem)
                                        <li class="list-group-item">
                                            <strong>{{ __('view.admin.catalog.items.show.id') }}: </strong>
                                            <p class="ms-3">{{ $tagItem->id }}</p>
                                            <strong>{{ __('view.admin.catalog.items.show.item_label') }}: </strong>
                                            <p class="ms-3">{{ $tagItem->item->name }}</p>
                                            <strong>{{ __('view.admin.catalog.items.show.tag_label') }}: </strong>
                                            <p class="ms-3">{{ $tagItem->Tag->name }}</p>
                                            <strong>{{ __('view.admin.catalog.items.show.validated') }}: </strong>
                                            <p class="ms-3">
                                                @if ($tagItem->validation == 1)
                                                    {{ __('view.admin.catalog.items.show.yes') }}
                                                @else
                                                    {{ __('view.admin.catalog.items.show.no') }}
                                                @endif
                                            </p>
                                            <strong>{{ __('view.admin.catalog.items.show.created_at') }}: </strong>
                                            <p class="ms-3">{{ date('d-m-Y H:i:s', strtotime($tagItem->created_at)) }}
                                            </p>
                                            <strong>{{ __('view.admin.catalog.items.show.updated_at') }}: </strong>
                                            <p class="ms-3">{{ date('d-m-Y H:i:s', strtotime($tagItem->updated_at)) }}
                                            </p>
                                            <div class="d-flex">
                                                <a href="{{ route('admin.item-tags.show', $tagItem->id) }}"
                                                    type="button" class="btn btn-primary me-1"><i
                                                        class="bi bi-eye-fill"></i> {{ __('view.admin.catalog.items.show.view') }}</a>
                                                <form action="{{ route('admin.item-tags.update', $tagItem->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-warning me-1"><i
                                                            class="bi bi-check2-circle"></i> {{ __('view.admin.catalog.items.show.validate_invalidate') }}</a>
                                                </form>
                                                <form action="{{ route('admin.item-tags.destroy', $tagItem->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="deleteItemTagButton btn btn-danger" type="submit" data-confirm-message="{{ __('view.admin.catalog.item_tags.index.delete_confirm') }}"><i
                                                            class="bi bi-trash-fill"></i> {{ __('view.admin.catalog.items.show.delete') }}
                                                </form>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <h5 class="card-header d-flex justify-content-between">{{ __('view.admin.catalog.items.show.related_components') }}<a
                                    type="button" class="btn btn-success"
                                    href="{{ route('admin.components.create', ['id' => $item->id, 'section' => $item->section]) }}"><i
                                        class="bi bi-plus-circle"></i> {{ __('view.admin.catalog.items.show.add_component') }}</a></h5>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    @foreach ($item->itemComponents as $itemComponent)
                                        <li class="list-group-item">
                                            <strong>{{ __('view.admin.catalog.items.show.id') }}: </strong>
                                            <p class="ms-3">{{ $itemComponent->id }}</p>
                                            <strong>{{ __('view.admin.catalog.items.show.main_item') }}: </strong>
                                            <p class="ms-3">{{ $itemComponent->item->name }}</p>
                                            <strong>{{ __('view.admin.catalog.items.show.component_label') }}: </strong>
                                            <p class="ms-3">{{ $itemComponent->component->name }}</p>
                                            <strong>{{ __('view.admin.catalog.items.show.validated') }}: </strong>
                                            <p class="ms-3">
                                                @if ($itemComponent->validation == 1)
                                                    {{ __('view.admin.catalog.items.show.yes') }}
                                                @else
                                                    {{ __('view.admin.catalog.items.show.no') }}
                                                @endif
                                            </p>
                                            <strong>{{ __('view.admin.catalog.items.show.created_at') }}: </strong>
                                            <p class="ms-3">
                                                {{ date('d-m-Y H:i:s', strtotime($itemComponent->created_at)) }}</p>
                                            <strong>{{ __('view.admin.catalog.items.show.updated_at') }}: </strong>
                                            <p class="ms-3">
                                                {{ date('d-m-Y H:i:s', strtotime($itemComponent->updated_at)) }}</p>
                                            <div class="d-flex">
                                                <a href="{{ route('admin.components.show', $itemComponent->id) }}"
                                                    type="button" class="btn btn-primary me-1"><i
                                                        class="bi bi-eye-fill"></i> {{ __('view.admin.catalog.items.show.view') }}</a>
                                                <form action="{{ route('admin.components.update', $itemComponent->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-warning me-1"><i
                                                            class="bi bi-check2-circle h6"></i> {{ __('view.admin.catalog.items.show.validate_invalidate') }}</a>
                                                </form>
                                                <form action="{{ route('admin.components.destroy', $itemComponent->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="deleteComponentButton btn btn-danger" type="submit"><i
                                                            class="bi bi-trash-fill"></i> {{ __('view.admin.catalog.items.show.delete') }}
                                                </form>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('image-modal.img-modal')


@endsection
