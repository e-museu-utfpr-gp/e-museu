<div class="modal fade" id="addExtraModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ __('view.catalog.items.create_modals.extra.title') }}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" id="addExtraForm" enctype="multipart/form-data">
                    @csrf
                    <input type="text" name="extra_id" id="extra-id" hidden>
                    <x-ui.inputs.textarea
                        name="extra-info"
                        id="extra-info"
                        :label="__('view.catalog.items.create_modals.extra.label')"
                        :help="__('view.catalog.items.create_modals.extra.help')"
                        :rows="6"
                        :showErrors="false"
                    />
                    <div class="col d-flex align-items-center justify-content-end">
                        <button class="button nav-link py-2 px-3 fw-bold" type="button"
                            id="save-extra-button">
                            {{ __('view.catalog.items.create_modals.extra.add') }}
                        </button>
                        <button class="button nav-link py-2 px-3 fw-bold" type="button"
                            id="update-extra-button" hidden>
                            {{ __('view.catalog.items.create_modals.extra.edit') }}
                        </button>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="cancel-button nav-link py-2 px-3 fw-bold" type="button" data-bs-dismiss="modal">
                    {{ __('view.catalog.items.create_modals.extra.cancel') }}
                </button>
            </div>
        </div>
    </div>
</div>
