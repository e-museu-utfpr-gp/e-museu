<div class="modal fade" id="addTagModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ __('view.catalog.items.create_modals.tag.title') }}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" id="addTagForm">
                    @csrf
                    <input type="text" name="tag_id" id="tag-id" hidden>
                    <x-ui.inputs.select
                        name="tag-category"
                        id="tag-category"
                        :label="__('view.catalog.items.create_modals.tag.category_label')"
                        :help="__('view.catalog.items.create_modals.tag.category_help')"
                        :roundedTop="true"
                        :showErrors="false"
                        onchange="checkIfCategoryIsEmpty()"
                    >
                        <option value="" selected>-</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </x-ui.inputs.select>
                    <x-ui.inputs.text
                        name="tag-name"
                        id="tag-name"
                        :label="__('view.catalog.items.create_modals.tag.name_label')"
                        :help="__('view.catalog.items.create_modals.tag.name_help')"
                        :roundedTop="true"
                        :showErrors="false"
                        class="typeahead"
                        onchange="checkTagName()"
                        disabled
                    />
                    <div class="warning-div px-1 mx-5 mb-3" id="tag-name-warning" hidden>
                        <i class="bi bi-exclamation-circle-fill mx-1 h5"></i>{{ __('view.catalog.items.create_modals.tag.not_registered') }}
                    </div>
                    <div class="col d-flex align-items-center justify-content-end">
                        <button class="button nav-link py-2 px-3 fw-bold" type="button" onclick="saveTag()"
                            id="save-tag-button">
                            {{ __('view.catalog.items.create_modals.tag.add') }}
                        </button>
                        <button class="button nav-link py-2 px-3 fw-bold" type="button" onclick="updateTag()"
                            id="update-tag-button" hidden>
                            {{ __('view.catalog.items.create_modals.tag.edit') }}
                        </button>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="cancel-button nav-link py-2 px-3 fw-bold" type="button" data-bs-dismiss="modal">
                    {{ __('view.catalog.items.create_modals.tag.cancel') }}
                </button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    let tagNameAutoCompletePath = "{{ route('catalog.tags.autocomplete') }}";
    let tagCheckName = "{{ route('catalog.tags.check-name') }}";
    let tagCount = 0;
    let tagIds = 1;

    function saveTag() {
        let tagCategoryText = $('#tag-category').find(":selected").text();
        let tagCategoryVal = $('#tag-category').find(":selected").val();
        let tagName = $('#tag-name').val();

        if (tagCategoryVal == '') {
            alert("{{ __('view.catalog.items.create_modals.tag.alert_category_required') }}");
            return;
        }

        if (tagName == '') {
            alert("{{ __('view.catalog.items.create_modals.tag.alert_name_required') }}");
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
            alert("{{ __('view.catalog.items.create_modals.tag.alert_category_required') }}");
            return;
        }

        if (tagName == '') {
            alert("{{ __('view.catalog.items.create_modals.tag.alert_name_required') }}");
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
        } else {
            $('#tag-name').prop('disabled', false);
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
        $("#tag-" + tagId).append(tagCategoryInput, tagNameInput, tagCard);
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
