<div class="modal fade" id="addExtraModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Adicionar um Informação Extra</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" id="addExtraForm" enctype="multipart/form-data">
                    <input type="text" name="extra_id" id="extra-id" hidden>
                    <label for="extra-info">
                        <h5>Informação Extra
                            <button type="button" class="info-icon btn border-0 bg-transparent px-0 py-0 mb-1"
                                data-bs-toggle="popover" data-bs-placement="top" data-bs-trigger="focus"
                                data-bs-content="Possui alguma informação adicional ou curiosidade sobre o item que gostaria de compartilhar? Por favor, nos informe por aqui.">
                                <i class="bi bi-info-circle-fill h4 ms-1" style="color: #ED6E38; cursor: pointer;"></i>
                            </button>
                        </h5>
                    </label>
                    <div class="input-div rounded-top">
                        <textarea class="form-control me-2 input-form @error('info') is-invalid @enderror" type="text" name="extra-info"
                            id="extra-info" placeholder="" rows="6"></textarea>
                    </div>
                    <div class="col d-flex align-items-center justify-content-end">
                        <button class="button nav-link py-2 px-3 fw-bold" type="button" onclick="saveExtra()"
                            id="save-extra-button">
                            Adicionar
                        </button>
                        <button class="button nav-link py-2 px-3 fw-bold" type="button" onclick="updateExtra()"
                            id="update-extra-button" hidden>
                            Editar
                        </button>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="cancel-button nav-link py-2 px-3 fw-bold" type="button" data-bs-dismiss="modal">
                    Cancelar
                </button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    let extraCount = 0;
    let extraIds = 1;

    function saveExtra() {
        let extraInfo = $('#extra-info').val();

        if (extraInfo == '') {
            alert("O campo curiosidade precisa ser preenchido!");
            return;
        }

        extraBuilder(extraInfo, extraIds);

        sessionStorage.setItem("itemCreateForm", "true");
        sessionStorage.setItem("extra" + extraIds + "info", extraInfo);

        extraCount++;
        extraIds++;

        sessionStorage.setItem("extraCount", extraCount);

        checkExtras();
        $('#addExtraModal').modal('hide');
    }

    function editExtra(extraId) {
        let input = $('#extra-' + extraId + ' > input').val();

        $('#extra-id').attr('value', extraId);
        $('#extra-info').val(input);

        $('#save-extra-button').prop("hidden", true);
        $('#update-extra-button').prop("hidden", false);
    }

    function updateExtra() {
        let extraInfo = $('#extra-info').val();
        let extraId = $('#extra-id').val();

        if (extraInfo == '') {
            alert("O campo curiosidade precisa ser preenchido!");
            return;
        }

        $('#extra-info-' + extraId).val(extraInfo);
        $('#extra-info-text-' + extraId).text(extraInfo);

        sessionStorage.setItem("extra" + extraId + "info", extraInfo);

        $('#addExtraModal').modal('hide');
    }

    function deleteExtra(extraId) {
        $('#extra-' + extraId).remove();
        extraCount--;

        sessionStorage.removeItem("extra" + extraId + "info");
        sessionStorage.setItem("extraCount", extraCount);

        checkExtras();
    }

    function checkExtras() {
        if (extraCount > 0) {
            $('#extra-empty-text').hide();

            if (extraCount > 9) {
                $('#add-extra-button').hide();
                $('#extra-full-text').prop('hidden', false);
            } else {
                $('#add-extra-button').show();
                $('#extra-full-text').prop('hidden', true);
            }
        } else {
            $('#extra-empty-text').show();
            sessionStorage.clear();
        }

        $('#extra-count-text').text(extraCount + "/10");
    }

    function extraBuilder(extraInfo, extraId) {
        let extraDiv = '<div class="extra" id="extra-' + extraId + '"></div>';

        let extraInfoInput = '<input type="text" name="extras[' + extraId + '][info]" id="extra-info-' + extraId +
            '" value="' + extraInfo + '" hidden>';

        let extraCard = `<div class="col s-2 m-2 d-flex justify-content-center">
                    <div class="card-body tag-card mw-100 mh-100 p-2">
                        <p class="card-subtitle mb-1" id="extra-info-text-` + extraId + `">` + extraInfo + `</p>
                    </div>
                    <button
                        class="edit-button d-flex align-items-center nav-link px-2 d-flex justify-content-center"
                        onclick="editExtra(` + extraId + `)"
                        data-bs-toggle="modal"
                        data-bs-target="#addExtraModal"
                        type="button"
                    >
                        <i class="bi bi-pencil align-middle h4"></i>
                    </button>
                    <button
                        class="cancel-button d-flex align-items-center nav-link px-2 d-flex justify-content-center"
                        onclick="deleteExtra(` + extraId + `)"
                        type="button"
                    >
                        <i class="bi bi-trash align-middle h4"></i>
                    </button>
                </div>`;

        $("#extras").append(extraDiv);
        $("#extra-" + extraIds).append(extraInfoInput, extraCard);
    }

    $('#addExtraModal').on('hidden.bs.modal', function() {
        $('#extra-info').val('');

        $('#save-extra-button').prop("hidden", false);
        $('#update-extra-button').prop("hidden", true);
    });
</script>
