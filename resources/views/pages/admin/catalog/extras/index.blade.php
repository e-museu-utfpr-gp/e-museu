<x-layouts.admin :title="__('view.admin.catalog.extras.index.title')"
    :heading="__('view.admin.catalog.extras.index.heading', ['count' => $count])">
            <x-admin.index-toolbar
                :create-href="route('admin.catalog.extras.create')"
                :create-label="__('view.admin.catalog.extras.index.add_extra')"
                :search-action="route('admin.catalog.extras.index')"
                :search-options="$searchOptions"
                :search-placeholder="__('view.admin.catalog.extras.index.search_placeholder')"
                :boolean-columns="$searchBooleanColumns"
            />
            <x-admin.sortable-table :action="route('admin.catalog.extras.index')" :columns="$sortColumns">
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
                                            <x-ui.buttons.admin.view href="{{ route('admin.catalog.extras.show', $extra->id) }}"
                                                class="me-1" />
                                            <x-ui.buttons.admin.edit href="{{ route('admin.catalog.extras.edit', $extra->id) }}"
                                                class="me-1" />
                                            <form action="{{ route('admin.catalog.extras.destroy', $extra->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <x-ui.buttons.admin.delete class="deleteExtraButton"
                                                    data-confirm-message="{{ __('view.admin.catalog.extras.index.delete_confirm') }}" />
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
            </x-admin.sortable-table>
            {{ $extras->links('pagination::bootstrap-5') }}
</x-layouts.admin>
