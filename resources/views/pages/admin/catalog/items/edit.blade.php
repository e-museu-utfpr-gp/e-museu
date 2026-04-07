@php
    $headingCode = $preferredContentTabLanguageCode ?? '';
    $headingName = $headingCode !== ''
        ? old('translations.' . $headingCode . '.name', $headingTranslation?->name ?? '—')
        : ($headingTranslation?->name ?? '—');
@endphp
<x-layouts.admin :title="__('view.admin.catalog.items.edit.title') . ' ' . $item->id"
    :heading="__('view.admin.catalog.items.edit.heading', ['id' => $item->id, 'name' => $headingName])">
            <form action="{{ route('admin.catalog.items.update', $item->id) }}" method="POST" enctype="multipart/form-data" id="admin-item-edit-form"
                data-original-identification-code="{{ $item->identification_code }}"
                data-label-cover="{{ __('app.catalog.item_image.cover') }}"
                data-label-gallery="{{ __('app.catalog.item_image.gallery') }}"
                data-identification-code-change-warning="{{ __('view.admin.catalog.items.edit.identification_code_change_modal_message') }}"
            >
                @csrf
                @method('PATCH')
                <div id="admin-delete-image-ids"></div>
                <input type="hidden" name="set_cover_image_id" id="set_cover_image_id" value="">
                @include('pages.admin.catalog.items._partials.translation-tabs', [
                    'contentLanguages' => $contentLanguages,
                    'preferredContentTabLanguageId' => $preferredContentTabLanguageId,
                    'item' => $item,
                ])
                <div class="row">
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-6">
                                <x-ui.inputs.admin.select
                                    name="category_id"
                                    id="category_id"
                                    :label="__('view.admin.catalog.items.edit.item_category')"
                                    required
                                >
                                    @foreach ($itemCategories as $itemCategory)
                                        <option value="{{ $itemCategory->id }}" @selected(old('category_id', $item->category_id) == $itemCategory->id)>
                                            {{ $itemCategory->name }}
                                        </option>
                                    @endforeach
                                </x-ui.inputs.admin.select>
                                <x-ui.inputs.admin.select
                                    name="collaborator_id"
                                    id="collaborator_id"
                                    :label="__('view.admin.catalog.items.edit.collaborator')"
                                    required
                                >
                                    @foreach ($collaborators as $collaborator)
                                        <option value="{{ $collaborator->id }}" @selected(old('collaborator_id', $item->collaborator_id) == $collaborator->id)>
                                            {{ $collaborator->email }} - {{ $collaborator->full_name }}
                                        </option>
                                    @endforeach
                                </x-ui.inputs.admin.select>
                                <x-ui.inputs.admin.select
                                    name="location_id"
                                    id="location_id"
                                    :label="__('view.admin.catalog.items.edit.location')"
                                    required
                                    :enhanced="false"
                                >
                                    @foreach ($locations as $location)
                                        <option value="{{ $location->id }}" @selected((string) old('location_id', $item->location_id) === (string) $location->id)>
                                            {{ $location->localized_label }}
                                        </option>
                                    @endforeach
                                </x-ui.inputs.admin.select>
                                <x-ui.inputs.admin.text
                                    name="date"
                                    id="date"
                                    type="date"
                                    :label="__('view.admin.catalog.items.edit.date')"
                                    :value="$item->date?->format('Y-m-d')"
                                />
                                <x-ui.inputs.admin.text
                                    name="identification_code"
                                    id="identification_code"
                                    :label="__('view.admin.catalog.items.edit.identification_code')"
                                    :value="$item->identification_code"
                                />
                                <p class="small text-warning mt-1">
                                    <i class="bi bi-exclamation-triangle-fill me-1" aria-hidden="true"></i>
                                    {{ __('view.admin.catalog.items.edit.identification_code_change_warning') }}
                                </p>
                                <x-ui.inputs.admin.select
                                    name="validation"
                                    id="validation"
                                    :label="__('view.admin.catalog.items.edit.validation')"
                                >
                                    <option value="0" @selected(old('validation', $item->validation) == 0)>{{ __('view.admin.catalog.items.edit.no') }}</option>
                                    <option value="1" @selected(old('validation', $item->validation) == 1)>{{ __('view.admin.catalog.items.edit.yes') }}</option>
                                </x-ui.inputs.admin.select>
                            </div>
                            <div class="col-md-6">
                                @php
                                    $allSortedEditImages = $item->images->sortBy('sort_order')->values();
                                    $sortedEditImages = $allSortedEditImages->filter(
                                        fn ($img) => $img->type !== \App\Enums\Catalog\ItemImageType::QRCODE
                                    )->values();
                                    $currentCoverImage = $sortedEditImages->first(
                                        fn ($img) => $img->type === \App\Enums\Catalog\ItemImageType::COVER
                                    ) ?? $sortedEditImages->first();
                                    $qrCodeImage = $qrCodeImage ?? $allSortedEditImages->first(
                                        fn ($img) => $img->type === \App\Enums\Catalog\ItemImageType::QRCODE
                                    );
                                @endphp
                                @include('pages.admin.catalog.items._partials.edit.cover-upload')
                                @include('pages.admin.catalog.items._partials.edit.gallery-upload')
                                @include('pages.admin.catalog.items._partials.edit.current-images-preview')
                                <div class="card mb-3">
                                    <h6 class="card-header">{{ __('view.admin.catalog.items.edit.qrcode') }}</h6>
                                    <div class="card-body">
                                        @if ($qrCodeImage)
                                            <img
                                                src="{{ $qrCodeImage->image_url }}"
                                                class="img-thumbnail mb-2"
                                                alt="{{ __('view.admin.catalog.items.edit.qrcode') }}"
                                                style="max-height: 180px;"
                                            >
                                        @else
                                            <p class="text-muted small mb-2">{{ __('view.admin.catalog.items.edit.qrcode_missing') }}</p>
                                        @endif

                                        <div class="small mb-2 text-break">
                                            <strong>{{ __('view.admin.catalog.items.edit.qrcode_target_url') }}:</strong>
                                            <a href="{{ $qrCodeTargetUrl }}" target="_blank" rel="noopener noreferrer">{{ $qrCodeTargetUrl }}</a>
                                        </div>
                                        @if ($qrDomainInvalid ?? false)
                                            <div class="alert alert-warning py-2 px-3 small mb-2">
                                                <i class="bi bi-exclamation-triangle-fill me-1" aria-hidden="true"></i>
                                                {{ __('view.admin.catalog.items.edit.qrcode_domain_invalid') }}
                                            </div>
                                        @endif

                                        <x-ui.buttons.submit
                                            type="submit"
                                            form="admin-item-regenerate-qrcode-form"
                                            variant="outline-primary"
                                            class="btn-sm"
                                            icon="bi bi-qr-code"
                                        >
                                            {{ $qrCodeImage ? __('view.admin.catalog.items.edit.qrcode_regenerate') : __('view.admin.catalog.items.edit.qrcode_generate') }}
                                        </x-ui.buttons.submit>
                                        @if ($qrCodeImage)
                                            <x-ui.buttons.submit
                                                type="submit"
                                                form="admin-item-delete-qrcode-form"
                                                variant="outline-danger"
                                                class="btn-sm ms-1"
                                                icon="bi bi-trash"
                                            >
                                                {{ __('view.admin.catalog.items.edit.qrcode_delete') }}
                                            </x-ui.buttons.submit>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <x-ui.buttons.submit variant="warning" icon="bi bi-pencil-fill">{{ __('view.admin.catalog.items.edit.submit') }}</x-ui.buttons.submit>
                </div>
                <x-release-lock-on-leave type="items" :id="$item->id" />
            </form>
        <form id="admin-item-regenerate-qrcode-form" action="{{ route('admin.catalog.items.qrcode.regenerate', $item->id) }}" method="POST" class="d-none">
            @csrf
        </form>
        <form id="admin-item-delete-qrcode-form" action="{{ route('admin.catalog.items.qrcode.delete', $item->id) }}" method="POST" class="d-none">
            @csrf
            @method('DELETE')
        </form>

        <div class="modal fade" id="identification-code-change-confirm-modal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('view.admin.catalog.items.edit.identification_code_change_modal_title') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('view.shared.modal_dismiss') }}"></button>
                    </div>
                    <div class="modal-body">
                        {{ __('view.admin.catalog.items.edit.identification_code_change_modal_message') }}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('view.shared.no') }}</button>
                        <button type="button" class="btn btn-warning" id="identification-code-change-confirm-submit">{{ __('view.shared.yes') }}</button>
                    </div>
                </div>
            </div>
        </div>

        <x-ui.image-modal />
        <x-ui.images.catalog.upload-assets />

</x-layouts.admin>
