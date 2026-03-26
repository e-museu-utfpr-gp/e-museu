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
                    <x-ui.inputs.select
                        name="component-category"
                        id="component-category"
                        :label="__('view.catalog.items.create_modals.component.category_label')"
                        :help="__('view.catalog.items.create_modals.component.category_help')"
                        :roundedTop="true"
                        :showErrors="false"
                        onchange="checkIfComponentCategoryIsEmpty()"
                    >
                        <option value="" selected>-</option>
                        @foreach ($itemCategories as $itemCategory)
                            <option value="{{ $itemCategory->id }}">{{ $itemCategory->name }}</option>
                        @endforeach
                    </x-ui.inputs.select>
                    <x-ui.inputs.select
                        name="component-name"
                        id="component-name"
                        :label="__('view.catalog.items.create_modals.component.name_label')"
                        :help="__('view.catalog.items.create_modals.component.name_help')"
                        :roundedTop="true"
                        :showErrors="false"
                        onchange="checkComponentName()"
                        disabled
                    >
                        <option value="" selected>-</option>
                    </x-ui.inputs.select>
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
    let componentGetByCategoryRoute = "{{ route('catalog.items.byCategory') }}";
    let componentCount = 0;
    let componentIds = 1;

    function getComponentI18n(path) {
        try {
            var parts = path.split('.');
            var obj = window.createModalsI18n;
            for (var i = 0; i < parts.length; i++) obj = obj[parts[i]];
            return obj || '';
        } catch (e) {
            return '';
        }
    }

    async function loadComponentsByCategory(categoryId) {
        try {
            var urlObj = new URL(componentGetByCategoryRoute, window.location.origin);
            urlObj.searchParams.set('item_category', categoryId);
            var res = await fetch(urlObj.toString(), { headers: { 'Accept': 'application/json' } });
            if (!res.ok) throw new Error('Request failed: ' + res.status);
            var data = await res.json();
            return Array.isArray(data) ? data : [];
        } catch (e) {
            console.error(e);
            return null;
        }
    }

    async function checkIfComponentCategoryIsEmpty() {
        var categoryId = $('#component-category').find(":selected").val();
        var select = $('#component-name');

        select.empty();
        select.append('<option value="" selected>-</option>');
        $('#component-name-warning').prop("hidden", true);
        $('#save-component-button').prop("disabled", true);

        if (!categoryId) {
            select.prop('disabled', true);
            return;
        }

        select.prop('disabled', false);

        var items = await loadComponentsByCategory(categoryId);
        if (items === null) {
            $('#component-name-warning').prop("hidden", false);
            select.prop('disabled', true);
            return;
        }

        if (items.length === 0) {
            $('#component-name-warning').prop("hidden", false);
            return;
        }

        items.forEach(function (it) {
            select.append('<option value="' + it.id + '">' + it.name + '</option>');
        });
    }

    function saveComponent() {
        let componentCategoryText = $('#component-category').find(":selected").text();
        let componentCategoryVal = $('#component-category').find(":selected").val();
        let componentName = $('#component-name').find(":selected").text().trim();
        let componentSelectedId = $('#component-name').find(":selected").val();

        if (componentCategoryVal == '') {
            alert(getComponentI18n('component.alert_category_required'));
            return;
        }

        if (componentSelectedId == '' || componentName == '-' || componentName == '') {
            alert(getComponentI18n('component.alert_name_required'));
            return;
        }

        $('#component-name-warning').prop("hidden", true);
        addComponentToList(componentCategoryText, componentCategoryVal, componentName);
        $('#addComponentModal').modal('hide');
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
        checkIfComponentCategoryIsEmpty().then(function () {
            // inputs[1] is the stored component name. Try to preselect by label.
            $('#component-name option').filter(function () { return $(this).text() === inputs[1]; }).prop('selected', true);
            checkComponentName();
        });

        $('#save-component-button').prop("hidden", true);
        $('#update-component-button').prop("hidden", false);
    }

    function updateComponent() {
        let componentCategoryText = $('#component-category').find(":selected").text();
        let componentCategoryVal = $('#component-category').find(":selected").val();
        let componentName = $('#component-name').find(":selected").text().trim();
        let componentSelectedId = $('#component-name').find(":selected").val();
        let componentId = $('#component-id').val();

        if (componentCategoryVal == '') {
            alert(getComponentI18n('component.alert_category_required'));
            return;
        }

        if (componentSelectedId == '' || componentName == '-' || componentName == '') {
            alert(getComponentI18n('component.alert_name_required'));
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
        $("#component-" + componentId).append(componentCategoryInput, componentNameInput, componentCard);
    }

    function checkComponentName() {
        var selectedId = $('#component-name').find(":selected").val();
        if (!selectedId) {
            $('#save-component-button').prop("disabled", true);
            return;
        }
        $('#save-component-button').prop("disabled", false);
    }

    $('#addComponentModal').on('hidden.bs.modal', function() {
        $('#component-category').val('');
        $('#component-name').empty().append('<option value="" selected>-</option>');

        $('#save-component-button').prop("hidden", false);
        $('#update-component-button').prop("hidden", true);
        $('#component-name').prop("disabled", true);
        $('#component-name-warning').prop("hidden", true);
    });
</script>
