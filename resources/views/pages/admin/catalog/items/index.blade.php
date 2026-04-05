<x-layouts.admin :title="__('view.admin.catalog.items.index.title')"
    :heading="__('view.admin.catalog.items.index.heading', ['count' => $count])">
            <x-admin.index-toolbar
                :create-href="route('admin.catalog.items.create')"
                :create-label="__('view.admin.catalog.items.index.add_item')"
                :search-action="route('admin.catalog.items.index')"
                :search-options="$searchOptions"
                :search-placeholder="__('view.admin.catalog.items.index.search_placeholder')"
                :boolean-columns="$searchBooleanColumns"
            />
            <x-admin.sortable-table :action="route('admin.catalog.items.index')" :columns="$sortColumns">
                            @foreach ($items as $item)
                                <tr class="@if (!$item->locks->isEmpty() && (string) $item->locks->first()->admin_id !== (string) auth()->id()) table-warning @endif">
                                    <th scope="row">{{ $item->id }}</th>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->description}}</td>
                                    <td>{{ $item->history }}</td>
                                    <td>{{ $item->detail }}</td>
                                    <td>{{ $item->date ? date('d-m-Y', strtotime($item->date)) : '—' }}</td>
                                    <td>{{ $item->identification_code }}</td>
                                    <td>
                                        @if ($item->validation == 1)
                                            {{ __('view.admin.catalog.items.index.yes') }}
                                        @else
                                            {{ __('view.admin.catalog.items.index.no') }}
                                        @endif
                                    </td>
                                    <td>{{ $item->item_category_name }}</td>
                                    <td>{{ $item->collaborator_email }}</td>
                                    <td>{{ date('d-m-Y H:i:s', strtotime($item->item_created)) }}</td>
                                    <td>{{ date('d-m-Y H:i:s', strtotime($item->item_updated)) }}</td>
                                    <td>
                                        <x-ui.buttons.admin.view href="{{ route('admin.catalog.items.show', $item->id) }}" />
                                        <x-ui.buttons.admin.edit href="{{ route('admin.catalog.items.edit', $item->id) }}" class="my-1" />
                                        <form action="{{ route('admin.catalog.items.destroy', $item->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <x-ui.buttons.admin.delete class="deleteItemButton"
                                                data-confirm-message="{{ __('view.admin.catalog.items.index.delete_confirm') }}" />
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
            </x-admin.sortable-table>
            <x-ui.pagination :paginator="$items" variant="success" class="mt-3" />
</x-layouts.admin>
