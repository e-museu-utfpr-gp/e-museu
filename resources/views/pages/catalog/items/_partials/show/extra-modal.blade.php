<div class="modal fade" id="addExtraModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">
                    {{ __('view.catalog.items.show_extra.title') }}
                </h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('catalog.extras.store') }}" method="POST" id="addExtraForm">
                    @csrf
                    <input name="item_id" value="{{ $item->id }}" hidden>
                    <x-ui.inputs.textarea
                        name="info"
                        id="info"
                        :label="__('view.catalog.items.show_extra.label')"
                        :help="__('view.catalog.items.show_extra.info_help')"
                        :rows="15"
                        required
                    />
                    <div>
                        <x-ui.inputs.text
                            name="contact"
                            id="contact"
                            type="email"
                            :label="__('view.catalog.items.show_extra.email_label')"
                            :help="__('view.catalog.items.show_extra.email_help')"
                            required
                            onchange="checkContact()"
                            onkeyup="checkContact()"
                        />
                        <div class="warning-div px-1 mx-5 mb-3" id="contact-warning" hidden>
                            <i class="bi bi-exclamation-circle-fill mx-1 h5"></i>
                            {{ __('view.catalog.items.show_extra.contact_warning') }}
                        </div>
                        <div class="success-div px-1 mx-5 mb-3" id="contact-success" hidden>
                            <i class="bi bi-exclamation-circle-fill mx-1 h5"></i>
                            {{ __('view.catalog.items.show_extra.contact_success') }}
                        </div>
                    </div>
                    <x-ui.inputs.text
                        name="full_name"
                        id="full_name"
                        :label="__('view.catalog.items.show_extra.full_name_label')"
                        :help="__('view.catalog.items.show_extra.full_name_help')"
                        required
                    />
                    <div class="col d-flex align-items-center justify-content-end">
                        <x-ui.buttons.submit variant="plain" class="button nav-link py-2 px-3 fw-bold" id="save-extra-button">
                            {{ __('view.catalog.items.show_extra.submit') }}
                        </x-ui.buttons.submit>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="cancel-button nav-link py-2 px-3 fw-bold" type="button" data-bs-dismiss="modal">
                    {{ __('view.catalog.items.show_extra.cancel') }}
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function checkContact() {
        $.ajax({
            type: "GET",
            url: checkContactRoute,
            data: {
                contact: $('#contact').val()
            },
            success: function(data) {
                if (data == false) {
                    $('#contact-warning').prop("hidden", false);
                    $('#contact-success').prop("hidden", true);
                    return;
                } else {
                    if ($('#contact').val() != '') {
                        $('#contact-warning').prop("hidden", true);
                        $('#contact-success').prop("hidden", false);
                        $('#full_name').val(data.full_name);
                    } else {
                        $('#contact-success').prop("hidden", true);
                        $('#contact-warning').prop("hidden", true);
                    }
                    return;
                }
            }
        });
    }
</script>
