<x-layouts.admin :title="__('view.admin.identity.admins.create.title')">
    <div class="mb-auto container-fluid">
        <x-ui.flash-messages />
        <form action="{{ route('admin.identity.admins.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-3">
                        <h2 class="card-header">{{ __('view.admin.identity.admins.create.heading') }}</h2>
                    </div>
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
                        <button type="submit" class="btn btn-success"><i class="bi bi-plus-circle"></i> {{ __('view.admin.identity.admins.create.submit') }}</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</x-layouts.admin>
