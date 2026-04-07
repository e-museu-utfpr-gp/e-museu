<div class="modal fade" id="addComponentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ __('view.catalog.items.create_modals.component.title') }}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" id="addComponentForm">
                    @csrf
                    <div id="component-modal-validation" class="alert alert-danger py-2 px-3 small mb-3 d-none"
                        role="alert" tabindex="-1"></div>
                    <input type="text" name="component_id" id="component-id" hidden>
                    <x-ui.inputs.select
                        name="component-category"
                        id="component-category"
                        :label="__('view.catalog.items.create_modals.component.category_label')"
                        :help="__('view.catalog.items.create_modals.component.category_help')"
                        :roundedTop="true"
                        :showErrors="false"
                    >
                        <option value="" selected>-</option>
                        @foreach ($itemCategories as $itemCategory)
                            <option value="{{ $itemCategory->id }}">{{ $itemCategory->name }}</option>
                        @endforeach
                    </x-ui.inputs.select>
                    <x-ui.inputs.select
                        name="component-name"
                        id="component-name"
                        :label="__('view.catalog.items.create_modals.component.name_label')"
                        :help="__('view.catalog.items.create_modals.component.name_help')"
                        :roundedTop="true"
                        :showErrors="false"
                        disabled
                    >
                        <option value="" selected>-</option>
                    </x-ui.inputs.select>
                    <div class="error-div px-1 mx-5 mb-3" id="component-name-warning" hidden>
                        <i class="bi bi-exclamation-circle-fill mx-1 h5"></i>{{ __('view.catalog.items.create_modals.component.not_found') }}
                    </div>
                    <div class="col d-flex align-items-center justify-content-end">
                        <button class="button nav-link py-2 px-3 fw-bold" type="button"
                            id="save-component-button" disabled>
                            {{ __('view.shared.buttons.add') }}
                        </button>
                        <button class="button nav-link py-2 px-3 fw-bold" type="button"
                            id="update-component-button" hidden>
                            {{ __('view.catalog.items.create_modals.component.edit') }}
                        </button>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="cancel-button nav-link py-2 px-3 fw-bold" type="button" data-bs-dismiss="modal">
                    {{ __('view.catalog.items.create_modals.component.cancel') }}
                </button>
            </div>
        </div>
    </div>
</div>
