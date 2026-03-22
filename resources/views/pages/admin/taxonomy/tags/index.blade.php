<x-layouts.admin :title="__('view.admin.taxonomy.tags.index.title')">
    <x-admin.index-shell :heading="__('view.admin.taxonomy.tags.index.heading', ['count' => $count])">
        <nav class="navbar navbar-light bg-light">
            <div class="container-fluid">
                <a href="{{ route('admin.taxonomy.tags.create') }}" type="button" class="btn btn-success"><i
                        class="bi bi-plus-circle"></i> {{ __('view.admin.taxonomy.tags.index.add_tag') }}</a>
                @php
                    $searchOptions = [
                        ['value' => 'id', 'label' => __('view.admin.taxonomy.tags.index.id')],
                        ['value' => 'name', 'label' => __('view.admin.taxonomy.tags.index.name')],
                        ['value' => 'validation', 'label' => __('view.admin.taxonomy.tags.index.validation')],
                        ['value' => 'tag_category_id', 'label' => __('view.admin.taxonomy.tags.index.category')],
                        ['value' => 'created_at', 'label' => __('view.admin.taxonomy.tags.index.created_at')],
                        ['value' => 'updated_at', 'label' => __('view.admin.taxonomy.tags.index.updated_at')],
                    ];
                @endphp
                <x-admin.search-form
                    :action="route('admin.taxonomy.tags.index')"
                    :options="$searchOptions"
                    :placeholder="__('view.admin.taxonomy.tags.index.search_placeholder')"
                    :buttonLabel="__('view.admin.taxonomy.tags.index.search_button')"
                    :booleanColumns="['validation']"
                />
            </div>
        </nav>
        <div class="row">
            <div class="col">
                <table class="table table-hover table-bordered">
                    <thead>
                        <form action="{{ route('admin.taxonomy.tags.index') }}" method="GET">
                            <tr>
                                <th scope="col"><button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="id">{{ __('view.admin.taxonomy.tags.index.id') }}</button></th>
                                <th scope="col"><button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="name">{{ __('view.admin.taxonomy.tags.index.name') }}</button></th>
                                <th scope="col"><button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="validation">{{ __('view.admin.taxonomy.tags.index.validation') }}</button></th>
                                <th scope="col"><button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="tag_category_id">{{ __('view.admin.taxonomy.tags.index.category') }}</button></th>
                                <th scope="col"><button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="created_at">{{ __('view.admin.taxonomy.tags.index.created_at') }}</button></th>
                                <th scope="col"><button class="btn border-0 bg-transparent px-0 py-0" type="submit"
                                        name="sort" value="updated_at">{{ __('view.admin.taxonomy.tags.index.updated_at') }}</button></th>
                            </tr>
                            <input name="order" value="@if (request()->query('order') == 'asc' || request()->query('order') == '') desc @else asc @endif" hidden>
                            <input name="search_column" value="{{ request()->query('search_column') }}" hidden>
                            <input name="search" value="{{ request()->query('search') }}" hidden>
                        </form>
                    </thead>
                    <tbody>
                        @foreach ($tags as $tag)
                            <tr class="@if (!$tag->locks->isEmpty() && $tag->locks->first()->user_id != auth()->user()->id) table-warning @endif">
                                <th scope="row">{{ $tag->id }}</th>
                                <td>{{ $tag->tag_name }}</td>
                                <td>
                                    @if ($tag->validation == 1)
                                        {{ __('view.admin.taxonomy.tags.index.yes') }}
                                    @else
                                        {{ __('view.admin.taxonomy.tags.index.no') }}
                                    @endif
                                </td>
                                <td>{{ $tag->category_name }}</td>
                                <td>{{ date('d-m-Y H:i:s', strtotime($tag->tag_created)) }}</td>
                                <td>{{ date('d-m-Y H:i:s', strtotime($tag->tag_updated)) }}</td>
                                <td>
                                    <div class="d-flex justify-content-center align-items-center">
                                        <a href="{{ route('admin.taxonomy.tags.show', $tag->id) }}" type="button"
                                            class="btn btn-primary me-1"><i class="bi bi-eye-fill"></i></a>
                                        <a href="{{ route('admin.taxonomy.tags.edit', $tag->id) }}" type="button"
                                            class="btn btn-warning me-1"><i class="bi bi-pencil-fill"></i></a>
                                        <form action="{{ route('admin.taxonomy.tags.destroy', $tag->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger deleteTagButton" data-confirm-message="{{ __('view.admin.taxonomy.tags.index.delete_confirm') }}"><i
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
        {{ $tags->links('pagination::bootstrap-5') }}
    </x-admin.index-shell>
</x-layouts.admin>
