<x-layouts.admin :title="__('view.admin.collaborator.collaborators.create.title')"
    :heading="__('view.admin.collaborator.collaborators.create.heading')">
        <form action="{{ route('admin.collaborators.store') }}" method="POST"
            autocomplete="off"
            data-admin-collaborator-form="1"
            data-check-contact-route="{{ route('admin.catalog.collaborators.check-contact') }}">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <x-ui.inputs.admin.text
                        name="full_name"
                        id="full_name"
                        autocomplete="off"
                        :label="__('view.admin.collaborator.collaborators.create.full_name')"
                    />
                    <x-ui.inputs.admin.text
                        name="email"
                        id="email"
                        type="email"
                        autocomplete="off"
                        :label="__('view.admin.collaborator.collaborators.create.email')"
                    />
                    <div class="alert alert-danger mb-3 py-2 small js-email-check-contact-network-error" hidden role="alert">
                        <i class="bi bi-wifi-off me-2" aria-hidden="true"></i>{{ __('view.admin.collaborator.collaborators.create.contact_check_network_error') }}
                    </div>
                    <div class="alert alert-danger mb-3 py-2 small" id="email-admin-duplicate" hidden role="alert">
                        <i class="bi bi-person-x me-2" aria-hidden="true"></i>{{ __('view.admin.collaborator.collaborators.create.email_duplicate_other_collaborator') }}
                    </div>
                    <x-ui.inputs.admin.select
                        name="role"
                        id="role"
                        :enhanced="false"
                        :label="__('view.admin.collaborator.collaborators.create.role')"
                    >
                        <option value="{{ \App\Enums\Collaborator\CollaboratorRole::EXTERNAL->value }}" @selected(old('role', \App\Enums\Collaborator\CollaboratorRole::EXTERNAL->value) === \App\Enums\Collaborator\CollaboratorRole::EXTERNAL->value)>
                            {{ __('app.collaborator.role.external') }}
                        </option>
                        <option value="{{ \App\Enums\Collaborator\CollaboratorRole::INTERNAL->value }}" @selected(old('role') === \App\Enums\Collaborator\CollaboratorRole::INTERNAL->value)>
                            {{ __('app.collaborator.role.internal') }}
                        </option>
                    </x-ui.inputs.admin.select>
                    <x-ui.inputs.admin.select
                        name="blocked"
                        id="blocked"
                        :enhanced="false"
                        :label="__('view.admin.collaborator.collaborators.create.blocked')"
                    >
                        <option value="0" @selected(old('blocked') == 0)>
                            {{ __('view.admin.collaborator.collaborators.create.no') }}
                        </option>
                        <option value="1" @selected(old('blocked') == 1)>
                            {{ __('view.admin.collaborator.collaborators.create.yes') }}
                        </option>
                    </x-ui.inputs.admin.select>
                    <x-ui.inputs.admin.text
                        name="last_email_verification_at"
                        id="last_email_verification_at"
                        type="datetime-local"
                        :label="__('view.admin.collaborator.collaborators.create.last_email_verification_at_label')"
                        :value="old('last_email_verification_at', '')"
                    />
                    <div class="mb-3">
                        <x-ui.buttons.default
                            type="button"
                            variant="secondary"
                            size="sm"
                            id="js-mark-email-verified-at-now"
                        >
                            {{ __('view.admin.collaborator.collaborators.create.mark_email_verified_now') }}
                        </x-ui.buttons.default>
                    </div>
                    <p class="form-text small text-muted mb-3">{{ __('view.admin.collaborator.collaborators.create.last_email_verification_at_help') }}</p>
                    <div class="mb-3">
                        <x-ui.buttons.submit variant="success" icon="bi bi-plus-circle">
                            {{ __('view.admin.collaborator.collaborators.create.submit') }}
                        </x-ui.buttons.submit>
                    </div>
                </div>
            </div>
        </form>
</x-layouts.admin>
