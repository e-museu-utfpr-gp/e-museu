<div class="modal fade" id="addComponentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Adicionar um Componente</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" id="addComponentForm">
                    @csrf
                    <input type="text" name="component_id" id="component-id" hidden>
                    <label for="component-category">
                        <h5>Categoria do Componente
                            <button type="button" class="info-icon btn border-0 bg-transparent px-0 py-0 mb-1"
                                data-bs-toggle="popover" data-bs-placement="top" data-bs-trigger="focus"
                                data-bs-content="A qual categoria, o componente a ser adicionado pertence?">
                                <i class="bi bi-info-circle-fill h4 ms-1" style="color: #ED6E38; cursor: pointer;"></i>
                            </button>
                        </h5>
                    </label>
                    <div class="input-div rounded-top">
                        <select class="form-select me-2 input-form" name="component-category" id="component-category"
                            onchange="checkIfComponentCategoryIsEmpty()">
                            <option selected="selected" value="">-</option>
                            @foreach ($sections as $section)
                                <option value="{{ $section->id }}">{{ $section->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <label for="name">
                        <h5>Nome do Componente
                            <button type="button" class="info-icon btn border-0 bg-transparent px-0 py-0 mb-1"
                                data-bs-toggle="popover" data-bs-placement="top" data-bs-trigger="focus"
                                data-bs-content="O nome do componente a ser relacionado ao item que está sendo cadastrado. Só é possível adicionar componentes que já estejão listados no nosso museu.">
                                <i class="bi bi-info-circle-fill h4 ms-1" style="color: #ED6E38; cursor: pointer;"></i>
                            </button>
                        </h5>
                    </label>
                    <div class="input-div rounded-top">
                        <input class="form-control typeahead me-2 input-form" type="text" name="component-name"
                            id="component-name" onchange="checkComponentName()" placeholder="" disabled>
                    </div>
                    <div class="error-div px-1 mx-5 mb-3" id="component-name-warning" hidden>
                        <i class="bi bi-exclamation-circle-fill mx-1 h5"></i>Não encontramos este item nos nossos
                        registros! O campo "categoria" e "nome" foram preenchidos corretamente?
                    </div>
                    <div class="col d-flex align-items-center justify-content-end">
                        <button class="button nav-link py-2 px-3 fw-bold" type="button" onclick="saveComponent()"
                            id="save-component-button" disabled>
                            Adicionar
                        </button>
                        <button class="button nav-link py-2 px-3 fw-bold" type="button" onclick="updateComponent()"
                            id="update-component-button" hidden>
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
    let componentNameAutoCompletePath = "{{ route('component-name-auto-complete') }}";
    let componentCheckName = "{{ route('check-component-name') }}";
    let componentCount = 0;
    let componentIds = 1;

    function saveComponent() {
        let componentCategoryText = $('#component-category').find(":selected").text();
        let componentCategoryVal = $('#component-category').find(":selected").val();
        let componentName = $('#component-name').val();

        if (componentCategoryVal == '') {
            alert("O campo categoria precisa de opção válida!");
            return;
        }

        if (componentName == '') {
            alert("O campo nome do componente precisa ser preenchida!");
            return;
        }

        componentBuilder(componentCategoryText, componentCategoryVal, componentName, componentIds);

        sessionStorage.setItem("itemCreateForm", "true");
        sessionStorage.setItem("component" + componentIds + "categoryText", componentCategoryText);
        sessionStorage.setItem("component" + componentIds + "categoryVal", componentCategoryVal);
        sessionStorage.setItem("component" + componentIds + "name", componentName);

        componentCount++;
        componentIds++;

        sessionStorage.setItem("componentCount", componentCount);

        checkComponents();
        $('#addComponentModal').modal('hide');
    }

    function editComponent(componentId) {
        let inputs = [];
        $('#component-' + componentId + ' > input').each(function() {
            inputs.push($(this).val());
        });

        $('#component-id').attr('value', componentId);
        $('#component-category').val(inputs[0]);
        $('#component-name').val(inputs[1]);

        checkIfComponentCategoryIsEmpty();
        checkComponentName();

        $('#save-component-button').prop("hidden", true);
        $('#update-component-button').prop("hidden", false);
    }

    function updateComponent() {
        let componentCategoryText = $('#component-category').find(":selected").text();
        let componentCategoryVal = $('#component-category').find(":selected").val();
        let componentName = $('#component-name').val();
        let componentId = $('#component-id').val();

        if (componentCategoryVal == '') {
            alert("O campo categoria precisa de opção válida!");
            return;
        }

        if (componentName == '') {
            alert("O campo nome de uma etiqueta precisa ser preenchida!");
            return;
        }

        $('#component-category' + componentId).val(componentCategoryVal);
        $('#component-category-text-' + componentId).text(componentCategoryText);
        $('#component-name' + componentId).val(componentName);
        $('#component-name-text-' + componentId).text(componentName);

        sessionStorage.setItem("component" + componentId + "categoryText", componentCategoryText);
        sessionStorage.setItem("component" + componentId + "categoryVal", componentCategoryVal);
        sessionStorage.setItem("component" + componentId + "name", componentName);

        $('#addComponentModal').modal('hide');
    }

    function deleteComponent(componentId) {
        $('#component-' + componentId).remove();
        componentCount--;

        sessionStorage.removeItem("component" + componentId + "categoryText");
        sessionStorage.removeItem("component" + componentId + "categoryVal");
        sessionStorage.removeItem("component" + componentId + "name");
        sessionStorage.setItem("componentCount", componentCount);

        checkComponents();
    }

    function checkIfComponentCategoryIsEmpty() {
        if ($('#component-category').find(":selected").val() == '') {
            $('#component-name').prop('disabled', true);
            $('#component-description').prop('disabled', true);
        } else {
            $('#component-name').prop('disabled', false);
            $('#component-description').prop('disabled', false);
        }
    }

    function checkComponents() {
        if (componentCount > 0) {
            $('#component-empty-text').hide();

            if (componentCount > 9) {
                $('#add-component-button').hide();
                $('#component-full-text').prop('hidden', false);
            } else {
                $('#add-component-button').show();
                $('#component-full-text').prop('hidden', true);
            }
        } else {
            $('#component-empty-text').show();
            sessionStorage.clear();
        }

        $('#component-count-text').text(componentCount + "/10");
    }

    function componentBuilder(componentCategoryText, componentCategoryVal, componentName, componentId) {
        let componentDiv = '<div class="component" id="component-' + componentId + '"></div>';

        let componentCategoryInput = '<input type="text" name="components[' + componentId +
            '][category_id]" id="category-component-' + componentId + '" value="' + componentCategoryVal + '" hidden>';
        let componentNameInput = '<input type="text" name="components[' + componentId + '][name]" id="name-component-' +
            componentId + '" value="' + componentName + '" hidden>';

        let componentCard = `<div class="col s-2 m-2 d-flex justify-content-center">
                            <div class="card-body tag-card mw-100 p-2">
                                <h6 class="card-title fw-bold border-dark" id="component-category-text-` +
            componentId + `">` + componentCategoryText + `</h6>
                                <p class="card-subtitle mb-1" id="component-name-text-` + componentId + `">` +
            componentName + `</p>
                            </div>
                            <button
                                class="edit-button d-flex align-items-center nav-link px-2 d-flex justify-content-center"
                                onclick="editComponent(` + componentId + `)"
                                data-bs-toggle="modal"
                                data-bs-target="#addComponentModal"
                                type="button"
                            >
                                <i class="bi bi-pencil align-middle h4"></i>
                            </button>
                            <button
                                class="cancel-button d-flex align-items-center nav-link px-2 d-flex justify-content-center"
                                onclick="deleteComponent(` + componentId + `)"
                                type="button"
                            >
                                <i class="bi bi-trash align-middle h4"></i>
                            </button>
                        </div>`;

        $("#components").append(componentDiv);
        $("#component-" + componentIds).append(componentCategoryInput, componentNameInput, componentCard);
    }

    function checkComponentName() {
        $.ajax({
            type: "GET",
            url: componentCheckName,
            data: {
                category: $('#component-category').val(),
                name: $('#component-name').val()
            },
            success: function(data) {
                if (data > 0) {
                    $('#component-name-warning').prop("hidden", true);
                    $('#save-component-button').prop("disabled", false);
                    return;
                } else {
                    $('#component-name-warning').prop("hidden", false);
                    $('#save-component-button').prop("disabled", true);
                    return;
                }
            }
        });
    }

    function initComponentAutocomplete() {
        if (typeof $ === 'undefined' || typeof $.fn.modernTypeahead === 'undefined') {
            setTimeout(initComponentAutocomplete, 100);
            return;
        }
        
        $('#component-name').modernTypeahead({
            source: function(query, process) {
                return $.get(componentNameAutoCompletePath, {
                    query: query,
                    category: $('#component-category').find(":selected").val()
                }, function(data) {
                    return process(data);
                });
            },
            minLength: 1,
            delay: 300
        });
    }
    
    initComponentAutocomplete();

    $('#addComponentModal').on('hidden.bs.modal', function() {
        $('#component-category').val('');
        $('#component-name').val('');

        $('#save-component-button').prop("hidden", false);
        $('#update-component-button').prop("hidden", true);
        $('#component-name').prop("disabled", true);
        $('#component-name-warning').prop("hidden", true);
    });
</script>
