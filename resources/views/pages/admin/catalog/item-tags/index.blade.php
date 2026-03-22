<x-layouts.admin :title="__('view.admin.catalog.item_tags.index.title')"
    :heading="__('view.admin.catalog.item_tags.index.heading', ['count' => $count])">
            <x-admin.index-toolbar
                :create-href="route('admin.catalog.item-tags.create')"
                :create-label="__('view.admin.catalog.item_tags.index.add')"
                :search-action="route('admin.catalog.item-tags.index')"
                :search-options="$searchOptions"
                :search-placeholder="__('view.admin.catalog.item_tags.index.search_placeholder')"
                :boolean-columns="$searchBooleanColumns"
            />
            <x-admin.sortable-table :action="route('admin.catalog.item-tags.index')" :columns="$sortColumns">
                            @foreach ($itemTags as $itemTag)
                                <tr>
                                    <th scope="row">{{ $itemTag->id }}</th>
                                    <td>{{ $itemTag->item_name }}</td>
                                    <td>{{ $itemTag->tag_name }}</td>
                                    <td>
                                        @if ($itemTag->item_tag_validation == 1)
                                            {{ __('view.admin.catalog.item_tags.index.yes') }}
                                        @else
                                            {{ __('view.admin.catalog.item_tags.index.no') }}
                                        @endif
                                    </td>
                                    <td>{{ date('d-m-Y H:i:s', strtotime($itemTag->item_tag_created)) }}</td>
                                    <td>{{ date('d-m-Y H:i:s', strtotime($itemTag->item_tag_updated)) }}</td>
                                    <td>
                                        <div class="d-flex justify-content-center align-items-center">
                                            <x-ui.buttons.view href="{{ route('admin.catalog.item-tags.show', $itemTag->id) }}"
                                                class="me-1" />
                                            <form action="{{ route('admin.catalog.item-tags.update', $itemTag->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <x-ui.buttons.validate-invalidate class="me-1" data-toggle="tooltip"
                                                    data-placement="top" title="{{ __('view.admin.catalog.item_tags.index.validate_invalidate_tooltip') }}" />
                                            </form>
                                            <form action="{{ route('admin.catalog.item-tags.destroy', $itemTag->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <x-ui.buttons.delete class="deleteItemTagButton"
                                                    data-confirm-message="{{ __('view.admin.catalog.item_tags.index.delete_confirm') }}" />
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
            </x-admin.sortable-table>
            {{ $itemTags->links('pagination::bootstrap-5') }}
</x-layouts.admin>
