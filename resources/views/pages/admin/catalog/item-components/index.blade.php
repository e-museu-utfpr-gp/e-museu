<x-layouts.admin :title="__('view.admin.catalog.components.index.title')"
    :heading="__('view.admin.catalog.components.index.heading', ['count' => $count])">
            <x-admin.index-toolbar
                :create-href="route('admin.catalog.item-components.create')"
                :create-label="__('view.admin.catalog.components.index.add_component')"
                :search-action="route('admin.catalog.item-components.index')"
                :search-options="$searchOptions"
                :search-placeholder="__('view.admin.catalog.components.index.search_placeholder')"
                :boolean-columns="$searchBooleanColumns"
            />
            <x-admin.sortable-table :action="route('admin.catalog.item-components.index')" :columns="$sortColumns">
                            @foreach ($itemComponents as $itemComponent)
                                <tr>
                                    <th scope="row">{{ $itemComponent->id }}</th>
                                    <td>{{ $itemComponent->item_name }}</td>
                                    <td>{{ $itemComponent->component_name }}</td>
                                    <td>
                                        @if ($itemComponent->item_component_validation == 1)
                                            {{ __('view.admin.catalog.components.index.yes') }}
                                        @else
                                            {{ __('view.admin.catalog.components.index.no') }}
                                        @endif
                                    </td>
                                    <td>{{ date('d-m-Y H:i:s', strtotime($itemComponent->item_component_created)) }}</td>
                                    <td>{{ date('d-m-Y H:i:s', strtotime($itemComponent->item_component_updated)) }}</td>
                                    <td>
                                        <div class="d-flex justify-content-center align-items-center">
                                            <x-ui.buttons.admin.view href="{{ route('admin.catalog.item-components.show', $itemComponent->id) }}"
                                                class="me-1" data-toggle="tooltip" data-placement="top"
                                                title="{{ __('view.admin.catalog.components.index.view_tooltip') }}" />
                                            <form action="{{ route('admin.catalog.item-components.update', $itemComponent->id) }}"
                                                method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <x-ui.buttons.admin.validate-invalidate class="me-1" data-toggle="tooltip"
                                                    data-placement="top" title="{{ __('view.admin.catalog.components.index.validate_tooltip') }}" />
                                            </form>
                                            <form action="{{ route('admin.catalog.item-components.destroy', $itemComponent->id) }}"
                                                method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <x-ui.buttons.admin.delete class="deleteComponentButton"
                                                    data-toggle="tooltip" data-placement="top" title="{{ __('view.admin.catalog.components.index.delete_tooltip') }}"
                                                    data-confirm-message="{{ __('view.admin.catalog.components.index.delete_confirm') }}" />
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
            </x-admin.sortable-table>
            {{ $itemComponents->links('pagination::bootstrap-5') }}
</x-layouts.admin>
