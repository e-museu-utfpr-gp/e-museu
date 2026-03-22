<x-layouts.admin :title="__('view.admin.collaborator.collaborators.edit.title', ['id' => $collaborator->id])"
    :heading="__('view.admin.collaborator.collaborators.edit.heading', ['id' => $collaborator->id, 'name' => $collaborator->full_name])">
        <form action="{{ route('admin.collaborators.update', $collaborator->id) }}" method="POST">
            @csrf
            @method('PATCH')
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="full_name" class="form-label">
                            {{ __('view.admin.collaborator.collaborators.edit.full_name') }}
                        </label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="full_name"
                            name="full_name" value="{{ $collaborator->full_name }}">
                        @error('full_name')
                            <div class="invalid-feedback"> {{ $message }} </div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="contact" class="form-label">
                            {{ __('view.admin.collaborator.collaborators.edit.contact') }}
                        </label>
                        <input type="email" class="form-control @error('contact') is-invalid @enderror" id="contact"
                            name="contact" value="{{ $collaborator->contact }}">
                        @error('contact')
                            <div class="invalid-feedback"> {{ $message }} </div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">
                            {{ __('view.admin.collaborator.collaborators.edit.role') }}
                        </label>
                        <select class="form-select @error('role') is-invalid @enderror" id="role" name="role">
                            @php
                                $roleValue = optional($collaborator->role)?->value ?? \App\Enums\Collaborator\CollaboratorRole::EXTERNAL->value;
                            @endphp
                            <option value="{{ \App\Enums\Collaborator\CollaboratorRole::EXTERNAL->value }}" @if ($roleValue === \App\Enums\Collaborator\CollaboratorRole::EXTERNAL->value) selected @endif>
                                {{ __('app.collaborator.role.external') }}
                            </option>
                            <option value="{{ \App\Enums\Collaborator\CollaboratorRole::INTERNAL->value }}" @if ($roleValue === \App\Enums\Collaborator\CollaboratorRole::INTERNAL->value) selected @endif>
                                {{ __('app.collaborator.role.internal') }}
                            </option>
                        </select>
                        @error('role')
                            <div class="invalid-feedback"> {{ $message }} </div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="blocked" class="form-label">
                            {{ __('view.admin.collaborator.collaborators.edit.blocked') }}
                        </label>
                        <select class="form-select @error('blocked') is-invalid @enderror" id="blocked" name="blocked">
                            <option value="0" @if ($collaborator->blocked == 0) selected @endif>
                                {{ __('view.admin.collaborator.collaborators.edit.no') }}
                            </option>
                            <option value="1" @if ($collaborator->blocked == 1) selected @endif>
                                {{ __('view.admin.collaborator.collaborators.edit.yes') }}
                            </option>
                        </select>
                        @error('blocked')
                            <div class="invalid-feedback"> {{ $message }} </div>
                        @enderror
                    </div>
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
