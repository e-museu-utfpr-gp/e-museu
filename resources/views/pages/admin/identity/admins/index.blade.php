<x-layouts.admin :title="__('view.admin.identity.admins.index.title')"
    :heading="__('view.admin.identity.admins.index.heading', ['count' => $count])">
        <x-admin.index-toolbar
            :create-href="route('admin.identity.admins.create')"
            :create-label="__('view.admin.identity.admins.index.add_admin')"
            :search-action="route('admin.identity.admins.index')"
            :search-options="$searchOptions"
            :search-placeholder="__('view.admin.identity.admins.index.search_placeholder')"
            :boolean-columns="$searchBooleanColumns"
        />
        <x-admin.sortable-table :action="route('admin.identity.admins.index')" :columns="$sortColumns">
                        @foreach ($admins as $admin)
                            <tr class="@if (!$admin->locks->isEmpty()) table-warning @endif">
                                <th scope="row">{{ $admin->id }}</th>
                                <td>{{ $admin->username }}</td>
                                <td>{{ date('d-m-Y H:i:s', strtotime($admin->created_at)) }}</td>
                                <td>{{ date('d-m-Y H:i:s', strtotime($admin->updated_at)) }}</td>
                                <td>
                                    <div class="d-flex justify-content-center align-items-center">
                                        <x-ui.buttons.view href="{{ route('admin.identity.admins.show', $admin->id) }}"
                                            class="me-1" />
                                        <form class="me-1" action="{{ route('admin.identity.admins.destroy', $admin->id) }}"
                                            method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <x-ui.buttons.delete class="deleteAdminButton"
                                                data-confirm-message="{{ __('view.admin.identity.admins.index.delete_confirm') }}" />
                                        </form>
                                        <form action="{{ route('admin.identity.admins.delete-lock', $admin->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <x-ui.buttons.default type="submit" variant="warning" class="deleteLockButton"
                                                title="{{ __('view.admin.identity.admins.index.unlock_tooltip') }}"
                                                icon="bi bi-unlock-fill" />
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
        </x-admin.sortable-table>
        {{ $admins->links('pagination::bootstrap-5') }}
</x-layouts.admin>
