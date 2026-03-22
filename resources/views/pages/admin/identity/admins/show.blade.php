<x-layouts.admin :title="__('view.admin.identity.admins.show.title') . ' ' . $admin->id"
    :heading="__('view.admin.identity.admins.show.heading', ['id' => $admin->id, 'username' => $admin->username])">
    <x-slot name="pageHeaderActions">
        <form action="{{ route('admin.identity.admins.destroy', $admin->id) }}" method="POST">
            @csrf
            @method('DELETE')
            <x-ui.buttons.delete class="deleteAdminButton"
                data-confirm-message="{{ __('view.admin.identity.admins.index.delete_confirm') }}" />
        </form>
    </x-slot>
        <div class="row">
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <h5 class="card-header">{{ __('view.admin.identity.admins.show.id') }}</h5>
                            <div class="card-body">
                                <p class="card-text">{{ $admin->id }}</p>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <h5 class="card-header">{{ __('view.admin.identity.admins.show.username') }}</h5>
                            <div class="card-body">
                                <p class="card-text">{{ $admin->username }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <h5 class="card-header">{{ __('view.admin.identity.admins.show.created_at') }}</h5>
                            <div class="card-body">
                                <p class="ms-2">{{ date('d-m-Y H:i:s', strtotime($admin->created_at)) }}</p>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <h5 class="card-header">{{ __('view.admin.identity.admins.show.updated_at') }}</h5>
                            <div class="card-body">
                                <p class="ms-2">{{ date('d-m-Y H:i:s', strtotime($admin->updated_at)) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

</x-layouts.admin>
