<x-layouts.admin :title="__('view.admin.catalog.item_categories.create.title')"
    :heading="__('view.admin.catalog.item_categories.create.heading')">
            <form action="{{ route('admin.catalog.item-categories.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        @include('pages.admin.catalog.item-categories._partials.translation-tabs', [
                            'contentLanguages' => $contentLanguages,
                            'preferredContentTabLanguageId' => $preferredContentTabLanguageId,
                            'itemCategory' => null,
                        ])
                        <div class="mb-3">
                            <x-ui.buttons.submit variant="success" icon="bi bi-plus-circle">
                                {{ __('view.admin.catalog.item_categories.create.submit') }}
                            </x-ui.buttons.submit>
                        </div>
                    </div>
                </div>
            </form>
</x-layouts.admin>
