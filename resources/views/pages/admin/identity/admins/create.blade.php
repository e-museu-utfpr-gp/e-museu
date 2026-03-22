<x-layouts.admin :title="__('view.admin.identity.admins.create.title')"
    :heading="__('view.admin.identity.admins.create.heading')">
        <form action="{{ route('admin.identity.admins.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="username" class="form-label">{{ __('view.admin.identity.admins.create.username') }}</label>
                        <input type="text" class="form-control @error('username') is-invalid @enderror" id="username"
                            name="username" value="{{ old('username') }}">
                        @error('username')
                            <div class="invalid-feedback"> {{ $message }} </div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">{{ __('view.admin.identity.admins.create.password') }}</label>
                        <input type="text" class="form-control @error('password') is-invalid @enderror" id="password"
                            name="password">
                        @error('password')
                            <div class="invalid-feedback"> {{ $message }} </div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <x-ui.buttons.submit variant="success" icon="bi bi-plus-circle">
                            {{ __('view.admin.identity.admins.create.submit') }}
                        </x-ui.buttons.submit>
                    </div>
                </div>
            </div>
        </form>
</x-layouts.admin>
