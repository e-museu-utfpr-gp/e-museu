<x-layouts.admin :title="__('view.admin.collaborator.collaborators.index.title')"
    :heading="__('view.admin.collaborator.collaborators.index.heading', ['count' => $count])">
        <x-admin.index-toolbar
            :create-href="route('admin.collaborators.create')"
            :create-label="__('view.admin.collaborator.collaborators.index.add')"
            :search-action="route('admin.collaborators.index')"
            :search-options="$searchOptions"
            :search-placeholder="__('view.admin.collaborator.collaborators.index.search_placeholder')"
            :boolean-columns="$searchBooleanColumns"
        />
        <x-admin.sortable-table :action="route('admin.collaborators.index')" :columns="$sortColumns">
                        @foreach ($collaborators as $collaborator)
                            <tr
                                class="@if (!$collaborator->locks->isEmpty() && (string) $collaborator->locks->first()->admin_id !== (string) auth()->id()) table-warning @endif">
                                <th scope="row">{{ $collaborator->id }}</th>
                                <td>{{ $collaborator->full_name }}</td>
                                <td>{{ $collaborator->email }}</td>
                                <td>{{ __('app.collaborator.role.' . (optional($collaborator->role)?->value ?? \App\Enums\Collaborator\CollaboratorRole::EXTERNAL->value)) }}</td>
                                <td>
                                    @if ($collaborator->blocked == 1)
                                        {{ __('view.admin.collaborator.collaborators.index.yes') }}
                                    @else
                                        {{ __('view.admin.collaborator.collaborators.index.no') }}
                                    @endif
                                </td>
                                <td>
                                    @if ($collaborator->last_email_verification_at)
                                        {{ $collaborator->last_email_verification_at->format('d-m-Y H:i:s') }}
                                    @else
                                        {{ __('view.admin.collaborator.collaborators.index.last_email_verification_empty') }}
                                    @endif
                                </td>
                                <td>{{ date('d-m-Y H:i:s', strtotime($collaborator->created_at)) }}</td>
                                <td>{{ date('d-m-Y H:i:s', strtotime($collaborator->updated_at)) }}</td>
                                <td>
                                    <div class="d-flex justify-content-center align-items-center">
                                        <x-ui.buttons.admin.view href="{{ route('admin.collaborators.show', $collaborator->id) }}"
                                            class="me-1" />
                                        <x-ui.buttons.admin.edit href="{{ route('admin.collaborators.edit', $collaborator->id) }}"
                                            class="me-1" />
                                        <form action="{{ route('admin.collaborators.destroy', $collaborator->id) }}"
                                            method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <x-ui.buttons.admin.delete class="deleteCollaboratorButton" />
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
        </x-admin.sortable-table>
        {{ $collaborators->links('pagination::bootstrap-5') }}
</x-layouts.admin>
