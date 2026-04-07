<x-layouts.admin :title="__('view.admin.collaborator.collaborators.edit.title', ['id' => $collaborator->id])"
    :heading="__('view.admin.collaborator.collaborators.edit.heading', ['id' => $collaborator->id, 'name' => $collaborator->full_name])">
        <form action="{{ route('admin.collaborators.update', $collaborator->id) }}" method="POST"
            autocomplete="off"
            data-admin-collaborator-form="1"
            data-current-collaborator-id="{{ $collaborator->id }}"
            data-check-contact-route="{{ route('admin.catalog.collaborators.check-contact') }}">
            @csrf
            @method('PATCH')
            <div class="row">
                <div class="col-md-6">
                    <x-ui.inputs.admin.text
                        name="full_name"
                        id="full_name"
                        autocomplete="off"
                        :label="__('view.admin.collaborator.collaborators.edit.full_name')"
                        :value="$collaborator->full_name"
                    />
                    <x-ui.inputs.admin.text
                        name="email"
                        id="email"
                        type="email"
                        autocomplete="off"
                        :label="__('view.admin.collaborator.collaborators.edit.email')"
                        :value="$collaborator->email"
                    />
                    <div class="alert alert-danger mb-3 py-2 small js-email-check-contact-network-error" hidden role="alert">
                        <i class="bi bi-wifi-off me-2" aria-hidden="true"></i>{{ __('view.admin.collaborator.collaborators.edit.contact_check_network_error') }}
                    </div>
                    <div class="alert alert-danger mb-3 py-2 small" id="email-admin-duplicate" hidden role="alert">
                        <i class="bi bi-person-x me-2" aria-hidden="true"></i>{{ __('view.admin.collaborator.collaborators.edit.email_duplicate_other_collaborator') }}
                    </div>
                    @php
                        $roleValue = optional($collaborator->role)?->value ?? \App\Enums\Collaborator\CollaboratorRole::EXTERNAL->value;
                    @endphp
                    <x-ui.inputs.admin.select
                        name="role"
                        id="role"
                        :enhanced="false"
                        :label="__('view.admin.collaborator.collaborators.edit.role')"
                    >
                        <option value="{{ \App\Enums\Collaborator\CollaboratorRole::EXTERNAL->value }}" @selected(old('role', $roleValue) === \App\Enums\Collaborator\CollaboratorRole::EXTERNAL->value)>
                            {{ __('app.collaborator.role.external') }}
                        </option>
                        <option value="{{ \App\Enums\Collaborator\CollaboratorRole::INTERNAL->value }}" @selected(old('role', $roleValue) === \App\Enums\Collaborator\CollaboratorRole::INTERNAL->value)>
                            {{ __('app.collaborator.role.internal') }}
                        </option>
                    </x-ui.inputs.admin.select>
                    <x-ui.inputs.admin.select
                        name="blocked"
                        id="blocked"
                        :enhanced="false"
                        :label="__('view.admin.collaborator.collaborators.edit.blocked')"
                    >
                        <option value="0" @selected(old('blocked', $collaborator->blocked) == 0)>
                            {{ __('view.admin.collaborator.collaborators.edit.no') }}
                        </option>
                        <option value="1" @selected(old('blocked', $collaborator->blocked) == 1)>
                            {{ __('view.admin.collaborator.collaborators.edit.yes') }}
                        </option>
                    </x-ui.inputs.admin.select>
                    <x-ui.inputs.admin.text
                        name="last_email_verification_at"
                        id="last_email_verification_at"
                        type="datetime-local"
                        :label="__('view.admin.collaborator.collaborators.edit.last_email_verification_at_label')"
                        :value="old('last_email_verification_at', $collaborator->last_email_verification_at?->format('Y-m-d\TH:i') ?? '')"
                    />
                    <div class="mb-3">
                        <x-ui.buttons.default
                            type="button"
                            variant="secondary"
                            size="sm"
                            id="js-mark-email-verified-at-now"
                        >
                            {{ __('view.admin.collaborator.collaborators.edit.mark_email_verified_now') }}
                        </x-ui.buttons.default>
                    </div>
                    <p class="form-text small text-muted mb-3">{{ __('view.admin.collaborator.collaborators.edit.last_email_verification_at_help') }}</p>
                    <div class="mb-3">
                        <x-ui.buttons.submit variant="warning" icon="bi bi-pencil-fill">
                            {{ __('view.admin.collaborator.collaborators.edit.submit') }}
                        </x-ui.buttons.submit>
                    </div>
                </div>
            </div>
            <x-release-lock-on-leave type="collaborators" :id="$collaborator->id" />
        </form>
</x-layouts.admin>
