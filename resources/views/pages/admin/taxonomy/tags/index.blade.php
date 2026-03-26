<x-layouts.admin :title="__('view.admin.taxonomy.tags.index.title')"
    :heading="__('view.admin.taxonomy.tags.index.heading', ['count' => $count])">
        <x-admin.index-toolbar
            :create-href="route('admin.taxonomy.tags.create')"
            :create-label="__('view.admin.taxonomy.tags.index.add_tag')"
            :search-action="route('admin.taxonomy.tags.index')"
            :search-options="$searchOptions"
            :search-placeholder="__('view.admin.taxonomy.tags.index.search_placeholder')"
            :boolean-columns="$searchBooleanColumns"
        />
        <x-admin.sortable-table :action="route('admin.taxonomy.tags.index')" :columns="$sortColumns">
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
                                        <x-ui.buttons.admin.view href="{{ route('admin.taxonomy.tags.show', $tag->id) }}"
                                            class="me-1" />
                                        <x-ui.buttons.admin.edit href="{{ route('admin.taxonomy.tags.edit', $tag->id) }}"
                                            class="me-1" />
                                        <form action="{{ route('admin.taxonomy.tags.destroy', $tag->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <x-ui.buttons.admin.delete class="deleteTagButton"
                                                data-confirm-message="{{ __('view.admin.taxonomy.tags.index.delete_confirm') }}" />
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
        </x-admin.sortable-table>
        {{ $tags->links('pagination::bootstrap-5') }}
</x-layouts.admin>
