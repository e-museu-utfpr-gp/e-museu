<div class="modal fade" id="addTagModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Adicionar uma Etiqueta</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" id="addTagForm">
                    @csrf
                    <input type="text" name="tag_id" id="tag-id" hidden>
                    <label for="tag-category">
                        <h5>Categoria da Etiqueta
                            <button type="button" class="info-icon btn border-0 bg-transparent px-0 py-0 mb-1"
                                data-bs-toggle="popover" data-bs-placement="top" data-bs-trigger="focus"
                                data-bs-content="As etiquetas providenciam mais opções de categorização para os itens. Um exemplo de categoria de etiqueta seria 'Marca'. A que categoria a etiqueta a ser relacionada pertence?">
                                <i class="bi bi-info-circle-fill h4 ms-1" style="color: #ED6E38; cursor: pointer;"></i>
                            </button>
                        </h5>
                    </label>
                    <div class="input-div rounded-top">
                        <select class="form-select me-2 input-form" name="tag-category" id="tag-category"
                            onchange="checkIfCategoryIsEmpty()">
                            <option selected="selected" value="">-</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <label for="name">
                        <h5>Nome da Etiqueta
                            <button type="button" class="info-icon btn border-0 bg-transparent px-0 py-0 mb-1"
                                data-bs-toggle="popover" data-bs-placement="top" data-bs-trigger="focus"
                                data-bs-content="O nome da etiqueta pertence à categoria de etiqueta selecionada. Seguindo o exemplo apresentado anteriormente, um exemplo de nome de etiqueta na categoria 'Marca' seria 'Samsung'.">
                                <i class="bi bi-info-circle-fill h4 ms-1" style="color: #ED6E38; cursor: pointer;"></i>
                            </button>
                        </h5>
                    </label>
                    <div class="input-div rounded-top">
                        <input class="form-control typeahead me-2 input-form" type="text" name="tag-name"
                            id="tag-name" onchange="checkTagName()" placeholder="" disabled>
                    </div>
                    <div class="warning-div px-1 mx-5 mb-3" id="tag-name-warning" hidden>
                        <i class="bi bi-exclamation-circle-fill mx-1 h5"></i>Ainda não temos esta etiqueta registrada!
                        Adicionaremos aos nossos registros após analisarmos sua sugestão.
                    </div>
                    <div class="col d-flex align-items-center justify-content-end">
                        <button class="button nav-link py-2 px-3 fw-bold" type="button" onclick="saveTag()"
                            id="save-tag-button">
                            Adicionar
                        </button>
                        <button class="button nav-link py-2 px-3 fw-bold" type="button" onclick="updateTag()"
                            id="update-tag-button" hidden>
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
    let tagNameAutoCompletePath = "{{ route('tag-name-auto-complete') }}";
    let tagCheckName = "{{ route('check-tag-name') }}";
    let tagCount = 0;
    let tagIds = 1;

    function saveTag() {
        let tagCategoryText = $('#tag-category').find(":selected").text();
        let tagCategoryVal = $('#tag-category').find(":selected").val();
        let tagName = $('#tag-name').val();

        if (tagCategoryVal == '') {
            alert("O campo categoria precisa de opção válida!");
            return;
        }

        if (tagName == '') {
            alert("O campo nome de uma etiqueta precisa ser preenchida!");
            return;
        }

        tagBuilder(tagCategoryText, tagCategoryVal, tagName, tagIds);

        sessionStorage.setItem("itemCreateForm", "true");
        sessionStorage.setItem("tag" + tagIds + "categoryText", tagCategoryText);
        sessionStorage.setItem("tag" + tagIds + "categoryVal", tagCategoryVal);
        sessionStorage.setItem("tag" + tagIds + "name", tagName);

        tagCount++;
        tagIds++;

        sessionStorage.setItem("tagCount", tagCount);

        checkTags();
        $('#addTagModal').modal('hide');
    }

    function editTag(tagId) {
        let inputs = [];
        $('#tag-' + tagId + ' > input').each(function() {
            inputs.push($(this).val());
        });

        $('#tag-id').attr('value', tagId);
        $('#tag-category').val(inputs[0]);
        $('#tag-name').val(inputs[1]);

        checkIfCategoryIsEmpty();
        checkTagName();

        $('#save-tag-button').prop("hidden", true);
        $('#update-tag-button').prop("hidden", false);
    }

    function updateTag() {
        let tagCategoryText = $('#tag-category').find(":selected").text();
        let tagCategoryVal = $('#tag-category').find(":selected").val();
        let tagName = $('#tag-name').val();
        let tagId = $('#tag-id').val();

        if (tagCategoryVal == '') {
            alert("O campo categoria precisa de opção válida!");
            return;
        }

        if (tagName == '') {
            alert("O campo nome de uma etiqueta precisa ser preenchida!");
            return;
        }

        $('#tag-category' + tagId).val(tagCategoryVal);
        $('#tag-category-text-' + tagId).text(tagCategoryText);
        $('#tag-name' + tagId).val(tagName);
        $('#tag-name-text-' + tagId).text(tagName);

        sessionStorage.setItem("tag" + tagId + "categoryText", tagCategoryText);
        sessionStorage.setItem("tag" + tagId + "categoryVal", tagCategoryVal);
        sessionStorage.setItem("tag" + tagId + "name", tagName);

        $('#addTagModal').modal('hide');
    }

    function deleteTag(tagId) {
        $('#tag-' + tagId).remove();
        tagCount--;

        sessionStorage.removeItem("tag" + tagId + "categoryText");
        sessionStorage.removeItem("tag" + tagId + "categoryVal");
        sessionStorage.removeItem("tag" + tagId + "name");
        sessionStorage.setItem("tagCount", tagCount);

        checkTags();
    }

    function checkIfCategoryIsEmpty() {
        if ($('#tag-category').find(":selected").val() == '') {
            $('#tag-name').prop('disabled', true);
            $('#tag-description').prop('disabled', true);
        } else {
            $('#tag-name').prop('disabled', false);
            $('#tag-description').prop('disabled', false);
        }
    }

    function checkTags() {
        if (tagCount > 0) {
            $('#tag-empty-text').hide();

            if (tagCount > 9) {
                $('#add-tag-button').hide();
                $('#tag-full-text').prop('hidden', false);
            } else {
                $('#add-tag-button').show();
                $('#tag-full-text').prop('hidden', true);
            }
        } else {
            $('#tag-empty-text').show();
            sessionStorage.clear();
        }

        $('#tag-count-text').text(tagCount + "/10");
    }

    function tagBuilder(tagCategoryText, tagCategoryVal, tagName, tagId) {
        let tagDiv = '<div class="tag" id="tag-' + tagId + '"></div>';

        let tagCategoryInput = '<input type="text" name="tags[' + tagId + '][category_id]" id="category-tag-' + tagId +
            '" value="' + tagCategoryVal + '" hidden>';
        let tagNameInput = '<input type="text" name="tags[' + tagId + '][name]" id="name-tag-' + tagId + '" value="' +
            tagName + '" hidden>';

        let tagCard = `<div class="col s-2 m-2 d-flex justify-content-center">
                            <div class="card-body tag-card mw-100 p-2">
                                <h6 class="card-title fw-bold border-dark" id="tag-category-text-` + tagId + `">` +
            tagCategoryText + `</h6>
                                <p class="card-subtitle mb-1" id="tag-name-text-` + tagId + `">` + tagName + `</p>
                            </div>
                            <button
                                class="edit-button d-flex align-items-center nav-link px-2 d-flex justify-content-center"
                                onclick="editTag(` + tagId + `)"
                                data-bs-toggle="modal"
                                data-bs-target="#addTagModal"
                                type="button"
                            >
                                <i class="bi bi-pencil align-middle h4"></i>
                            </button>
                            <button
                                class="cancel-button d-flex align-items-center nav-link px-2 d-flex justify-content-center"
                                onclick="deleteTag(` + tagId + `)"
                                type="button"
                            >
                                <i class="bi bi-trash align-middle h4"></i>
                            </button>
                        </div>`;

        $("#tags").append(tagDiv);
        $("#tag-" + tagIds).append(tagCategoryInput, tagNameInput, tagCard);
    }

    function checkTagName() {
        $.ajax({
            type: "GET",
            url: tagCheckName,
            data: {
                category: $('#tag-category').val(),
                name: $('#tag-name').val()
            },
            success: function(data) {
                if (data > 0) {
                    $('#tag-name-warning').prop("hidden", true);
                    return;
                } else {
                    $('#tag-name-warning').prop("hidden", false);
                    return;
                }
            }
        });
    }

    function initTagAutocomplete() {
        if (typeof $ === 'undefined' || typeof $.fn.modernTypeahead === 'undefined') {
            setTimeout(initTagAutocomplete, 100);
            return;
        }
        
        $('#tag-name').modernTypeahead({
            source: function(query, process) {
                return $.get(tagNameAutoCompletePath, {
                    query: query,
                    category: $('#tag-category').find(":selected").val()
                }, function(data) {
                    return process(data);
                });
            },
            minLength: 1,
            delay: 300
        });
    }
    
    initTagAutocomplete();

    $('#addTagModal').on('hidden.bs.modal', function() {
        $('#tag-category').val('');
        $('#tag-name').val('');

        $('#save-tag-button').prop("hidden", false);
        $('#update-tag-button').prop("hidden", true);
        $('#tag-name').prop("disabled", true);
        $('#tag-name-warning').prop("hidden", true);
    });
</script>
