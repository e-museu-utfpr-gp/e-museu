<x-layouts.admin :title="__('view.admin.collaborator.collaborators.edit.title', ['id' => $collaborator->id])"
    :heading="__('view.admin.collaborator.collaborators.edit.heading', ['id' => $collaborator->id, 'name' => $collaborator->full_name])">
        <form action="{{ route('admin.collaborators.update', $collaborator->id) }}" method="POST">
            @csrf
            @method('PATCH')
            <div class="row">
                <div class="col-md-6">
                    <x-ui.inputs.admin.text
                        name="full_name"
                        id="full_name"
                        :label="__('view.admin.collaborator.collaborators.edit.full_name')"
                        :value="$collaborator->full_name"
                    />
                    <x-ui.inputs.admin.text
                        name="contact"
                        id="contact"
                        type="email"
                        :label="__('view.admin.collaborator.collaborators.edit.contact')"
                        :value="$collaborator->contact"
                    />
                    @php
                        $roleValue = optional($collaborator->role)?->value ?? \App\Enums\Collaborator\CollaboratorRole::EXTERNAL->value;
                    @endphp
                    <x-ui.inputs.admin.select
                        name="role"
                        id="role"
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
                        :label="__('view.admin.collaborator.collaborators.edit.blocked')"
                    >
                        <option value="0" @selected(old('blocked', $collaborator->blocked) == 0)>
                            {{ __('view.admin.collaborator.collaborators.edit.no') }}
                        </option>
                        <option value="1" @selected(old('blocked', $collaborator->blocked) == 1)>
                            {{ __('view.admin.collaborator.collaborators.edit.yes') }}
                        </option>
                    </x-ui.inputs.admin.select>
                    <div class="mb-3">
                        <x-ui.buttons.submit variant="warning" icon="bi bi-pencil-fill">
                            {{ __('view.admin.collaborator.collaborators.edit.submit') }}
                        </x-ui.buttons.submit>
                    </div>
                </div>
            </div>
        </form>
    <x-release-lock-on-leave type="collaborators" :id="$collaborator->id" />
</x-layouts.admin>
