<div class="modal fade" id="addComponentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ __('view.catalog.items.create_modals.component.title') }}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" id="addComponentForm">
                    @csrf
                    <input type="text" name="component_id" id="component-id" hidden>
                    <label for="component-category">
                        <h5>{{ __('view.catalog.items.create_modals.component.category_label') }}
                            <x-ui.info-popover :content="__('view.catalog.items.create_modals.component.category_help')" />
                        </h5>
                    </label>
                    <div class="input-div rounded-top">
                        <select class="form-select me-2 input-form" name="component-category" id="component-category"
                            onchange="checkIfComponentCategoryIsEmpty()">
                            <option selected="selected" value="">-</option>
                            @foreach ($itemCategories as $itemCategory)
                                <option value="{{ $itemCategory->id }}">{{ $itemCategory->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <label for="name">
                        <h5>{{ __('view.catalog.items.create_modals.component.name_label') }}
                            <x-ui.info-popover :content="__('view.catalog.items.create_modals.component.name_help')" />
                        </h5>
                    </label>
                    <div class="input-div rounded-top">
                        <input class="form-control typeahead me-2 input-form" type="text" name="component-name"
                            id="component-name" onchange="checkComponentName()" oninput="checkComponentName()" placeholder="" disabled>
                    </div>
                    <div class="error-div px-1 mx-5 mb-3" id="component-name-warning" hidden>
                        <i class="bi bi-exclamation-circle-fill mx-1 h5"></i>{{ __('view.catalog.items.create_modals.component.not_found') }}
                    </div>
                    <div class="col d-flex align-items-center justify-content-end">
                        <button class="button nav-link py-2 px-3 fw-bold" type="button" onclick="saveComponent()"
                            id="save-component-button" disabled>
                            {{ __('view.shared.buttons.add') }}
                        </button>
                        <button class="button nav-link py-2 px-3 fw-bold" type="button" onclick="updateComponent()"
                            id="update-component-button" hidden>
                            {{ __('view.catalog.items.create_modals.component.edit') }}
                        </button>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="cancel-button nav-link py-2 px-3 fw-bold" type="button" data-bs-dismiss="modal">
                    {{ __('view.catalog.items.create_modals.component.cancel') }}
                </button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    let componentNameAutoCompletePath = "{{ route('catalog.items.component-autocomplete') }}";
    let componentCheckName = "{{ route('catalog.items.check-component-name') }}";
    let componentCount = 0;
    let componentIds = 1;

    function saveComponent() {
        let componentCategoryText = $('#component-category').find(":selected").text();
        let componentCategoryVal = $('#component-category').find(":selected").val();
        let componentName = $('#component-name').val().trim();

        if (componentCategoryVal == '') {
            alert("O campo categoria precisa de opção válida!");
            return;
        }

        if (componentName == '') {
            alert("O campo nome do componente precisa ser preenchida!");
            return;
        }

        $.ajax({
            type: "GET",
            url: componentCheckName,
            data: { category: componentCategoryVal, name: componentName },
            success: function(count) {
                if (count > 0) {
                    $('#component-name-warning').prop("hidden", true);
                    addComponentToList(componentCategoryText, componentCategoryVal, componentName);
                    $('#addComponentModal').modal('hide');
                } else {
                    $('#component-name-warning').prop("hidden", false);
                    $('#save-component-button').prop("disabled", true);
                }
            }
        });
    }

    function addComponentToList(componentCategoryText, componentCategoryVal, componentName) {
        componentBuilder(componentCategoryText, componentCategoryVal, componentName, componentIds);

        sessionStorage.setItem("itemCreateForm", "true");
        sessionStorage.setItem("component" + componentIds + "categoryText", componentCategoryText);
        sessionStorage.setItem("component" + componentIds + "categoryVal", componentCategoryVal);
        sessionStorage.setItem("component" + componentIds + "name", componentName);

        componentCount++;
        componentIds++;

        sessionStorage.setItem("componentCount", componentCount);

        checkComponents();
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
            $('#save-component-button').prop("disabled", true);
        } else {
            $('#component-name').prop('disabled', false);
            $('#component-description').prop('disabled', false);
            checkComponentName();
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
        var name = $('#component-name').val();
        if (!name || name.trim() === '') {
            $('#save-component-button').prop("disabled", true);
            return;
        }
        $('#save-component-button').prop("disabled", false);
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
