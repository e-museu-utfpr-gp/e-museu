<x-layouts.app :title="__('view.admin.auth.login.title')">
    <div class="container mb-auto mt-3">
        <h2>{{ __('view.admin.auth.login.heading') }}</h2>

        <div class="row">
            <div class="col-6">
                <form action="{{ route('login') }}" method="POST">
                    @csrf
                    <x-ui.inputs.admin.text
                        name="username"
                        id="username"
                        :label="__('view.admin.auth.login.username_label')"
                        required
                        autofocus
                    />
                    <x-ui.inputs.admin.password
                        name="password"
                        id="password"
                        autocomplete="current-password"
                        :label="__('view.admin.auth.login.password_label')"
                        required
                    />
                    <div class="mb-3">
                        <x-ui.buttons.submit variant="primary">{{ __('view.admin.auth.login.submit') }}</x-ui.buttons.submit>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
