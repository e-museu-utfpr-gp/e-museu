<x-layouts.admin :title="__('view.admin.catalog.item_categories.index.title')"
    :heading="__('view.admin.catalog.item_categories.index.heading', ['count' => $count])">
            <x-admin.index-toolbar
                :create-href="route('admin.catalog.item-categories.create')"
                :create-label="__('view.admin.catalog.item_categories.index.add_item_category')"
                :search-action="route('admin.catalog.item-categories.index')"
                :search-options="$searchOptions"
                :search-placeholder="__('view.admin.catalog.item_categories.index.search_placeholder')"
                :boolean-columns="$searchBooleanColumns"
            />
            <x-admin.sortable-table :action="route('admin.catalog.item-categories.index')" :columns="$sortColumns">
                            @foreach ($itemCategories as $itemCategory)
                                <tr class="@if (!$itemCategory->locks->isEmpty() && (string) $itemCategory->locks->first()->admin_id !== (string) auth()->id()) table-warning @endif">
                                    <th scope="row">{{ $itemCategory->id }}</th>
                                    <td>{{ $itemCategory->name }}</td>
                                    <td>{{ date('d-m-Y H:i:s', strtotime($itemCategory->created_at)) }}</td>
                                    <td>{{ date('d-m-Y H:i:s', strtotime($itemCategory->updated_at)) }}</td>
                                    <td>
                                        <div class="d-flex justify-content-center align-items-center">
                                            <x-ui.buttons.admin.view href="{{ route('admin.catalog.item-categories.show', $itemCategory->id) }}"
                                                class="me-1" />
                                            <x-ui.buttons.admin.edit href="{{ route('admin.catalog.item-categories.edit', $itemCategory->id) }}"
                                                class="me-1" />
                                            <form action="{{ route('admin.catalog.item-categories.destroy', $itemCategory->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <x-ui.buttons.admin.delete class="deleteItemCategoryButton"
                                                    data-confirm-message="{{ __('view.admin.catalog.item_categories.delete_confirm') }}" />
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
            </x-admin.sortable-table>
            {{ $itemCategories->links('pagination::bootstrap-5') }}
</x-layouts.admin>
