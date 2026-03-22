<x-layouts.admin :title="__('view.admin.catalog.extras.index.title')">
    <x-admin.index-shell :heading="__('view.admin.catalog.extras.index.heading', ['count' => $count])">
        <nav class="navbar navbar-light bg-light">
            <div class="container-fluid">
                <a href="{{ route('admin.catalog.extras.create') }}" type="button" class="btn btn-success"><i
                        class="bi bi-plus-circle"></i> {{ __('view.admin.catalog.extras.index.add_extra') }}</a>
                @php
                    $searchOptions = [
                        ['value' => 'id', 'label' => __('view.admin.catalog.extras.index.id')],
                        ['value' => 'info', 'label' => __('view.admin.catalog.extras.index.info')],
                        ['value' => 'item_id', 'label' => __('view.admin.catalog.extras.index.item')],
                        ['value' => 'collaborator_id', 'label' => __('view.admin.catalog.extras.index.collaborator')],
                        ['value' => 'validation', 'label' => __('view.admin.catalog.extras.index.validation')],
                        ['value' => 'created_at', 'label' => __('view.admin.catalog.extras.index.created_at')],
                        ['value' => 'updated_at', 'label' => __('view.admin.catalog.extras.index.updated_at')],
                    ];
                @endphp
                <x-admin.search-form
                    :action="route('admin.catalog.extras.index')"
                    :options="$searchOptions"
                    :placeholder="__('view.admin.catalog.extras.index.search_placeholder')"
                    :buttonLabel="__('view.admin.catalog.extras.index.search_button')"
                    :booleanColumns="['validation']"
                />
            </div>
        </nav>
        <div class="row">
            <div class="col">
                <table class="table table-hover table-bordered">
                    <thead>
                        <form action="{{ route('admin.catalog.extras.index') }}" method="GET">
                            <tr>
                                <th scope="col"><button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="id">{{ __('view.admin.catalog.extras.index.id') }}</button></th>
                                <th scope="col"><button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="info">{{ __('view.admin.catalog.extras.index.info') }}</button></th>
                                <th scope="col"><button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="item_id">{{ __('view.admin.catalog.extras.index.item') }}</button></th>
                                <th scope="col"><button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="collaborator_id">{{ __('view.admin.catalog.extras.index.collaborator') }}</button></th>
                                <th scope="col"><button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="validation">{{ __('view.admin.catalog.extras.index.validation') }}</button></th>
                                <th scope="col"><button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="created_at">{{ __('view.admin.catalog.extras.index.created_at') }}</button></th>
                                <th scope="col"><button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="updated_at">{{ __('view.admin.catalog.extras.index.updated_at') }}</button></th>
                            </tr>
                            <input name="order" value="@if (request()->query('order') == 'asc' || request()->query('order') == '') desc @else asc @endif" hidden>
                            <input name="search_column" value="{{ request()->query('search_column') }}" hidden>
                            <input name="search" value="{{ request()->query('search') }}" hidden>
                        </form>
                    </thead>
                    <tbody>
                        @foreach ($extras as $extra)
                            <tr class="@if (!$extra->locks->isEmpty() && $extra->locks->first()->user_id != auth()->user()->id) table-warning @endif">
                                <th scope="row">{{ $extra->id }}</th>
                                <td>{{ $extra->info }}</td>
                                <td>{{ $extra->item_name }}</td>
                                <td>{{ $extra->collaborator_contact }}</td>
                                <td>
                                    @if ($extra->extra_validation == 1)
                                        {{ __('view.admin.catalog.extras.index.yes') }}
                                    @else
                                        {{ __('view.admin.catalog.extras.index.no') }}
                                    @endif
                                </td>
                                <td>{{ date('d-m-Y H:i:s', strtotime($extra->extra_created)) }}</td>
                                <td>{{ date('d-m-Y H:i:s', strtotime($extra->extra_updated)) }}</td>
                                <td>
                                    <div class="d-flex justify-content-center align-items-center">
                                        <a href="{{ route('admin.catalog.extras.show', $extra->id) }}" type="button"
                                            class="btn btn-primary me-1"><i class="bi bi-eye-fill"></i></a>
                                        <a href="{{ route('admin.catalog.extras.edit', $extra->id) }}" type="button"
                                            class="btn btn-warning me-1"><i class="bi bi-pencil-fill"></i></a>
                                        <form action="{{ route('admin.catalog.extras.destroy', $extra->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger deleteExtraButton" data-confirm-message="{{ __('view.admin.catalog.extras.index.delete_confirm') }}"><i
                                                    class="bi bi-trash-fill"></i>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        {{ $extras->links('pagination::bootstrap-5') }}
    </x-admin.index-shell>
</x-layouts.admin>
