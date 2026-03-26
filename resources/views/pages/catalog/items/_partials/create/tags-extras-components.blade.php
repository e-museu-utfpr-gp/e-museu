<div>
    <div class="d-flex justify-content-between">
        <h5>{{ __('view.catalog.items.create.tags_label') }}
            <x-ui.info-popover :content="__('view.catalog.items.create.tags_help')" />
        </h5>
        <h4 class="me-2" id="tag-count-text">0/10</h4>
    </div>
    <div class="tagContainer mb-4">
        <div class="tags ms-3" id="tags">
            <p class="text-center p-1 empty-text" id="tag-empty-text">{{ __('view.catalog.items.create.tags_empty') }}</p>
        </div>
        <div class="d-flex justify-content-center mt-3">
            <div class="warning-div px-1 mx-5 mb-3" id="tag-full-text" hidden>
                <i class="bi bi-exclamation-circle-fill mx-1 h5"></i>{{ __('view.catalog.items.create.tags_limit') }}
            </div>
            @include('pages.catalog.items._partials.create.modal-plus-button', [
                'target' => '#addTagModal',
                'id' => 'add-tag-button',
            ])
        </div>
    </div>
</div>

<div>
    <div class="d-flex justify-content-between">
        <h5>{{ __('view.catalog.items.create.extra_label') }}
            <x-ui.info-popover :content="__('view.catalog.items.create.extra_help')" />
        </h5>
        <h4 class="me-2" id="extra-count-text">0/10</h4>
    </div>
    <div class="extraContainer mb-4">
        <div class="extras ms-3" id="extras">
            <p class="text-center p-1 empty-text" id="extra-empty-text">{{ __('view.catalog.items.create.extra_empty') }}</p>
        </div>
        <div class="d-flex justify-content-center mt-3">
            <div class="warning-div px-1 mx-5 mb-3" id="extra-full-text" hidden>
                <i class="bi bi-exclamation-circle-fill mx-1 h5"></i>{{ __('view.catalog.items.create.extra_limit') }}
            </div>
            @include('pages.catalog.items._partials.create.modal-plus-button', [
                'target' => '#addExtraModal',
                'id' => 'add-extra-button',
            ])
        </div>
    </div>
</div>

<div>
    <div class="d-flex justify-content-between">
        <h5>{{ __('view.catalog.items.create.components_label') }}
            <x-ui.info-popover :content="__('view.catalog.items.create.components_help')" />
        </h5>
        <h4 class="me-2" id="component-count-text">0/10</h4>
    </div>
    <div class="componentContainer mb-4">
        <div class="components ms-3" id="components">
            <p class="text-center p-1 empty-text" id="component-empty-text">{{ __('view.catalog.items.create.components_empty') }}</p>
        </div>
        <div class="d-flex justify-content-center mt-3">
            <div class="warning-div px-1 mx-5 mb-3" id="component-full-text" hidden>
                <i class="bi bi-exclamation-circle-fill mx-1 h5"></i>{{ __('view.catalog.items.create.components_limit') }}
            </div>
            @include('pages.catalog.items._partials.create.modal-plus-button', [
                'target' => '#addComponentModal',
                'id' => 'add-component-button',
            ])
        </div>
    </div>
</div>

<div class="col d-flex align-items-center justify-content-end">
    <x-ui.buttons.submit variant="plain" class="button nav-link py-2 px-3 fw-bold">{{ __('view.catalog.items.create.submit') }}</x-ui.buttons.submit>
</div>

