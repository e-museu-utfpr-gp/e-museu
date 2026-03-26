<x-layouts.admin :title="__('view.admin.identity.admins.create.title')"
    :heading="__('view.admin.identity.admins.create.heading')">
        <form action="{{ route('admin.identity.admins.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <x-ui.inputs.admin.text
                        name="username"
                        id="username"
                        :label="__('view.admin.identity.admins.create.username')"
                    />
                    <x-ui.inputs.admin.password
                        name="password"
                        id="password"
                        autocomplete="new-password"
                        :label="__('view.admin.identity.admins.create.password')"
                    />
                    <div class="mb-3">
                        <x-ui.buttons.submit variant="success" icon="bi bi-plus-circle">
                            {{ __('view.admin.identity.admins.create.submit') }}
                        </x-ui.buttons.submit>
                    </div>
                </div>
            </div>
        </form>
</x-layouts.admin>
