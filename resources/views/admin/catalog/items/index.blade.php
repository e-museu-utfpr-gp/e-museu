@extends('layouts.admin')
@section('title', __('view.admin.catalog.items.index.title'))

@section('content')
    <div class="mb-auto container-fluid">
        @if (session('success'))
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
        @endif
        @foreach ($errors->all() as $error)
            <div class="alert alert-danger" role="alert">
                {{ $error }}
            </div>
        @endforeach
        <div class="card mb-3">
            <h2 class="card-header">{{ __('view.admin.catalog.items.index.heading', ['count' => $count]) }}</h2>
        </div>
        <nav class="navbar navbar-light bg-light">
            <div class="container-fluid">
                <a href="{{ route('admin.items.create') }}" type="button" class="btn btn-success"><i
                        class="bi bi-plus-circle"></i> {{ __('view.admin.catalog.items.index.add_item') }}</a>
                @php
                    $searchOptions = [
                        ['value' => 'id', 'label' => __('view.admin.catalog.items.index.search_option_id')],
                        ['value' => 'name', 'label' => __('view.admin.catalog.items.index.search_option_name')],
                        ['value' => 'description', 'label' => __('view.admin.catalog.items.index.search_option_description')],
                        ['value' => 'history', 'label' => __('view.admin.catalog.items.index.search_option_history')],
                        ['value' => 'detalhes', 'label' => __('view.admin.catalog.items.index.search_option_detail')],
                        ['value' => 'date', 'label' => __('view.admin.catalog.items.index.search_option_date')],
                        ['value' => 'identification_code', 'label' => __('view.admin.catalog.items.index.search_option_identification_code')],
                        ['value' => 'validation', 'label' => __('view.admin.catalog.items.index.search_option_validation')],
                        ['value' => 'collaborator_id', 'label' => __('view.admin.catalog.items.index.search_option_collaborator')],
                        ['value' => 'category_id', 'label' => __('view.admin.catalog.items.index.search_option_item_category')],
                        ['value' => 'created_at', 'label' => __('view.admin.catalog.items.index.search_option_created_at')],
                        ['value' => 'updated_at', 'label' => __('view.admin.catalog.items.index.search_option_updated_at')],
                    ];
                @endphp
                <x-admin.search-form
                    :action="route('admin.items.index')"
                    :options="$searchOptions"
                    :placeholder="__('view.admin.catalog.items.index.search_placeholder')"
                    :buttonLabel="__('view.admin.catalog.items.index.search_button')"
                    :booleanColumns="['validation']"
                />
            </div>
        </nav>
        <div class="row">
            <div class="col">
                <table class="table table-hover table-bordered">
                    <thead>
                        <form action="{{ route('admin.items.index') }}" method="GET">
                            <tr>
                                <th scope="col"><button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="id">{{ __('view.admin.catalog.items.index.sort_id') }}</button></th>
                                <th scope="col"><button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="name">{{ __('view.admin.catalog.items.index.sort_name') }}</button></th>
                                <th scope="col"><button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="description">{{ __('view.admin.catalog.items.index.sort_description') }}</button></th>
                                <th scope="col"><button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="history">{{ __('view.admin.catalog.items.index.sort_history') }}</button></th>
                                <th scope="col"><button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="detail">{{ __('view.admin.catalog.items.index.sort_detail') }}</button></th>
                                <th scope="col"><button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="date">{{ __('view.admin.catalog.items.index.sort_date') }}</button></th>
                                <th scope="col"><button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="identification_code">{{ __('view.admin.catalog.items.index.sort_code') }}</button></th>
                                <th scope="col"><button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="validation">{{ __('view.admin.catalog.items.index.sort_validation') }}</button></th>
                                <th scope="col"><button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="category_id">{{ __('view.admin.catalog.items.index.sort_item_category') }}</button></th>
                                <th scope="col"><button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="collaborator_id">{{ __('view.admin.catalog.items.index.sort_collaborator') }}</button></th>
                                <th scope="col"><button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="created_at">{{ __('view.admin.catalog.items.index.created_at') }}</button></th>
                                <th scope="col"><button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="updated_at">{{ __('view.admin.catalog.items.index.updated_at') }}</button></th>
                                <th scope="col"></th>
                            </tr>
                            <input name="order" value="@if (request()->query('order') == 'asc' || request()->query('order') == '') desc @else asc @endif"
                                hidden>
                            <input name="search_column" value="{{ request()->query('search_column') }}" hidden>
                            <input name="search" value="{{ request()->query('search') }}" hidden>
                        </form>
                    </thead>
                    <tbody>
                        @foreach ($items as $item)
                            <tr class="@if (!$item->locks->isEmpty() && $item->locks->first()->user_id != auth()->user()->id) table-warning @endif">
                                <th scope="row">{{ $item->id }}</th>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->description}}</td>
                                <td>{{ $item->history }}</td>
                                <td>{{ $item->detail }}</td>
                                <td>{{ $item->date ? date('d-m-Y', strtotime($item->date)) : '—' }}</td>
                                <td>{{ $item->identification_code }}</td>
                                <td>
                                    @if ($item->item_validation == 1)
                                        {{ __('view.admin.catalog.items.index.yes') }}
                                    @else
                                        {{ __('view.admin.catalog.items.index.no') }}
                                    @endif
                                </td>
                                <td>{{ $item->item_category_name }}</td>
                                <td>{{ $item->collaborator_contact }}</td>
                                <td>{{ date('d-m-Y H:i:s', strtotime($item->item_created)) }}</td>
                                <td>{{ date('d-m-Y H:i:s', strtotime($item->item_updated)) }}</td>
                                <td>
                                    <a href="{{ route('admin.items.show', $item->id) }}" type="button"
                                        class="btn btn-primary"><i class="bi bi-eye-fill"></i></a>
                                    <a href="{{ route('admin.items.edit', $item->id) }}" type="button"
                                        class="btn btn-warning my-1"><i class="bi bi-pencil-fill"></i></a>
                                    <form action="{{ route('admin.items.destroy', $item->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger deleteItemButton" data-confirm-message="{{ __('view.admin.catalog.items.index.delete_confirm') }}"><i
                                                class="bi bi-trash-fill"></i>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        {{ $items->links('pagination::bootstrap-5') }}
    </div>
@endsection
