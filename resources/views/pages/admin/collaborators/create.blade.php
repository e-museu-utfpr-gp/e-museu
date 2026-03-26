<x-layouts.admin :title="__('view.admin.collaborator.collaborators.create.title')"
    :heading="__('view.admin.collaborator.collaborators.create.heading')">
        <form action="{{ route('admin.collaborators.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <x-ui.inputs.admin.text
                        name="full_name"
                        id="full_name"
                        :label="__('view.admin.collaborator.collaborators.create.full_name')"
                    />
                    <x-ui.inputs.admin.text
                        name="contact"
                        id="contact"
                        type="email"
                        :label="__('view.admin.collaborator.collaborators.create.contact')"
                    />
                    <x-ui.inputs.admin.select
                        name="role"
                        id="role"
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
                        :label="__('view.admin.collaborator.collaborators.create.blocked')"
                    >
                        <option value="0" @selected(old('blocked') == 0)>
                            {{ __('view.admin.collaborator.collaborators.create.no') }}
                        </option>
                        <option value="1" @selected(old('blocked') == 1)>
                            {{ __('view.admin.collaborator.collaborators.create.yes') }}
                        </option>
                    </x-ui.inputs.admin.select>
                    <div class="mb-3">
                        <x-ui.buttons.submit variant="success" icon="bi bi-plus-circle">
                            {{ __('view.admin.collaborator.collaborators.create.submit') }}
                        </x-ui.buttons.submit>
                    </div>
                </div>
            </div>
        </form>
</x-layouts.admin>
