<div class="modal fade" id="addTagModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ __('view.catalog.items.create_modals.tag.title') }}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" id="addTagForm">
                    @csrf
                    <div id="tag-modal-validation" class="alert alert-danger py-2 px-3 small mb-3 d-none" role="alert"
                        tabindex="-1"></div>
                    <input type="text" name="tag_id" id="tag-id" hidden>
                    <x-ui.inputs.select
                        name="tag-category"
                        id="tag-category"
                        :label="__('view.catalog.items.create_modals.tag.category_label')"
                        :help="__('view.catalog.items.create_modals.tag.category_help')"
                        :roundedTop="true"
                        :showErrors="false"
                    >
                        <option value="" selected>-</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </x-ui.inputs.select>
                    <x-ui.inputs.text
                        name="tag-name"
                        id="tag-name"
                        :label="__('view.catalog.items.create_modals.tag.name_label')"
                        :help="__('view.catalog.items.create_modals.tag.name_help')"
                        :roundedTop="true"
                        :showErrors="false"
                        class="typeahead"
                        disabled
                    />
                    <div class="warning-div px-1 mx-5 mb-3" id="tag-name-warning" hidden>
                        <i class="bi bi-exclamation-circle-fill mx-1 h5"></i>{{ __('view.catalog.items.create_modals.tag.not_registered') }}
                    </div>
                    <div class="col d-flex align-items-center justify-content-end">
                        <button class="button nav-link py-2 px-3 fw-bold" type="button"
                            id="save-tag-button">
                            {{ __('view.catalog.items.create_modals.tag.add') }}
                        </button>
                        <button class="button nav-link py-2 px-3 fw-bold" type="button"
                            id="update-tag-button" hidden>
                            {{ __('view.catalog.items.create_modals.tag.edit') }}
                        </button>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="cancel-button nav-link py-2 px-3 fw-bold" type="button" data-bs-dismiss="modal">
                    {{ __('view.catalog.items.create_modals.tag.cancel') }}
                </button>
            </div>
        </div>
    </div>
</div>
