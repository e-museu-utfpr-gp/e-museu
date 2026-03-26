<x-layouts.admin :title="__('view.admin.taxonomy.tag_categories.index.title')"
    :heading="__('view.admin.taxonomy.tag_categories.index.heading', ['count' => $count])">
        <x-admin.index-toolbar
            :create-href="route('admin.taxonomy.tag-categories.create')"
            :create-label="__('view.admin.taxonomy.tag_categories.index.add_tag_category')"
            :search-action="route('admin.taxonomy.tag-categories.index')"
            :search-options="$searchOptions"
            :search-placeholder="__('view.admin.taxonomy.tag_categories.index.search_placeholder')"
            :boolean-columns="$searchBooleanColumns"
        />
        <x-admin.sortable-table :action="route('admin.taxonomy.tag-categories.index')" :columns="$sortColumns">
                        @foreach ($tagCategories as $tagCategory)
                            <tr class="@if (!$tagCategory->locks->isEmpty() && $tagCategory->locks->first()->admin_id != auth()->user()->id) table-warning @endif">
                                <th scope="row">{{ $tagCategory->id }}</th>
                                <td>{{ $tagCategory->name }}</td>
                                <td>{{ date('d-m-Y H:i:s', strtotime($tagCategory->created_at)) }}</td>
                                <td>{{ date('d-m-Y H:i:s', strtotime($tagCategory->updated_at)) }}</td>
                                <td>
                                    <div class="d-flex justify-content-center align-items-center">
                                        <x-ui.buttons.admin.view href="{{ route('admin.taxonomy.tag-categories.show', $tagCategory->id) }}"
                                            class="me-1" />
                                        <x-ui.buttons.admin.edit href="{{ route('admin.taxonomy.tag-categories.edit', $tagCategory->id) }}"
                                            class="me-1" />
                                        <form action="{{ route('admin.taxonomy.tag-categories.destroy', $tagCategory->id) }}"
                                            method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <x-ui.buttons.admin.delete class="deleteCategoryButton"
                                                data-confirm-message="{{ __('view.admin.taxonomy.tag_categories.delete_confirm') }}" />
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
        </x-admin.sortable-table>
        {{ $tagCategories->links('pagination::bootstrap-5') }}
</x-layouts.admin>
