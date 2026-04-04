import $ from 'jquery';
import {
    appendWizardOrder,
    clearTagWizardSectionStorage,
    removeWizardOrderId,
    wizardRemoveItem,
    wizardSetItem,
} from '../../../../../shared/catalog/item-create-storage';
import { hideBootstrapModal } from '../../../../../shared/catalog/hide-bootstrap-modal';
import { getItemCreateForm, parseDataModalsI18n } from '../../../../../shared/catalog/item-create-modal-helpers';

function i18nTag(key) {
    const t = parseDataModalsI18n().tag || {};
    return t[key] || '';
}

function clearTagModalValidation() {
    const el = document.getElementById('tag-modal-validation');
    if (el) {
        el.classList.add('d-none');
        el.textContent = '';
    }
}

function showTagModalValidation(message) {
    const el = document.getElementById('tag-modal-validation');
    if (!el) {
        return;
    }
    el.textContent = message;
    el.classList.remove('d-none');
    el.focus();
}

function contributionContentLocale() {
    const el = document.getElementById('contribution_content_locale');
    return el ? String(el.value || '') : '';
}

function routesFromForm() {
    const form = getItemCreateForm();
    if (!form) {
        return { autocomplete: '', checkName: '' };
    }
    return {
        autocomplete: form.getAttribute('data-route-tags-autocomplete') || '',
        checkName: form.getAttribute('data-route-tags-check-name') || '',
    };
}

/** Native select (enhanced-select moves id to the toggle; never use $('#tag-category').) */
function $tagCategory() {
    return $('#addTagModal select[name="tag-category"]');
}

window.tagCount = 0;
window.tagIds = 1;

let checkTagNamePending = null;

function saveTag() {
    const tagCategoryText = $tagCategory().find(':selected').text();
    const tagCategoryVal = $tagCategory().find(':selected').val();
    const tagName = String($('#tag-name').val() ?? '').trim();

    if (tagCategoryVal === '') {
        showTagModalValidation(i18nTag('alert_category_required'));
        return;
    }

    if (tagName === '') {
        showTagModalValidation(i18nTag('alert_name_required'));
        return;
    }

    const assignedId = window.tagIds;
    window.tagBuilder(tagCategoryText, tagCategoryVal, tagName, assignedId);

    wizardSetItem('itemCreateForm', 'true');
    wizardSetItem('tag' + assignedId + 'categoryText', tagCategoryText);
    wizardSetItem('tag' + assignedId + 'categoryVal', String(tagCategoryVal));
    wizardSetItem('tag' + assignedId + 'name', tagName);
    appendWizardOrder('tagIdOrder', assignedId);

    window.tagCount++;
    window.tagIds++;

    wizardSetItem('tagCount', String(window.tagCount));

    clearTagModalValidation();
    window.checkTags();
    hideBootstrapModal('#addTagModal');
}

function editTag(tagId) {
    const inputs = [];
    $('#tag-' + tagId + ' > input').each(function () {
        inputs.push($(this).val());
    });

    $('#tag-id').attr('value', tagId);
    $tagCategory().val(inputs[0]).trigger('change');
    $('#tag-name').val(inputs[1]);

    checkIfCategoryIsEmpty();
    checkTagName();

    $('#save-tag-button').prop('hidden', true);
    $('#update-tag-button').prop('hidden', false);
}

function updateTag() {
    const tagCategoryText = $tagCategory().find(':selected').text();
    const tagCategoryVal = $tagCategory().find(':selected').val();
    const tagName = String($('#tag-name').val() ?? '').trim();
    const tagId = $('#tag-id').val();

    if (tagCategoryVal === '') {
        showTagModalValidation(i18nTag('alert_category_required'));
        return;
    }

    if (tagName === '') {
        showTagModalValidation(i18nTag('alert_name_required'));
        return;
    }

    $('#category-tag-' + tagId).val(tagCategoryVal);
    $('#tag-category-text-' + tagId).text(tagCategoryText);
    $('#name-tag-' + tagId).val(tagName);
    $('#tag-name-text-' + tagId).text(tagName);

    wizardSetItem('tag' + tagId + 'categoryText', tagCategoryText);
    wizardSetItem('tag' + tagId + 'categoryVal', String(tagCategoryVal));
    wizardSetItem('tag' + tagId + 'name', tagName);

    clearTagModalValidation();
    hideBootstrapModal('#addTagModal');
}

function deleteTag(tagId) {
    $('#tag-' + tagId).remove();
    window.tagCount--;

    wizardRemoveItem('tag' + tagId + 'categoryText');
    wizardRemoveItem('tag' + tagId + 'categoryVal');
    wizardRemoveItem('tag' + tagId + 'name');
    removeWizardOrderId('tagIdOrder', tagId);
    wizardSetItem('tagCount', String(window.tagCount));

    window.checkTags();
}

function checkIfCategoryIsEmpty() {
    if ($tagCategory().find(':selected').val() === '') {
        $('#tag-name').prop('disabled', true);
    } else {
        $('#tag-name').prop('disabled', false);
    }
}

window.checkTags = function checkTags() {
    if (window.tagCount > 0) {
        $('#tag-empty-text').hide();

        if (window.tagCount > 9) {
            $('#add-tag-button').hide();
            $('#tag-full-text').prop('hidden', false);
        } else {
            $('#add-tag-button').show();
            $('#tag-full-text').prop('hidden', true);
        }
    } else {
        $('#tag-empty-text').show();
        clearTagWizardSectionStorage();
    }

    $('#tag-count-text').text(window.tagCount + '/10');
};

window.tagBuilder = function tagBuilder(tagCategoryText, tagCategoryVal, tagName, tagId) {
    const id = Number(tagId);
    const root = document.createElement('div');
    root.className = 'tag';
    root.id = 'tag-' + id;

    const catInput = document.createElement('input');
    catInput.type = 'text';
    catInput.name = 'tags[' + id + '][category_id]';
    catInput.id = 'category-tag-' + id;
    catInput.value = String(tagCategoryVal ?? '');
    catInput.hidden = true;

    const nameInput = document.createElement('input');
    nameInput.type = 'text';
    nameInput.name = 'tags[' + id + '][name]';
    nameInput.id = 'name-tag-' + id;
    nameInput.value = String(tagName ?? '');
    nameInput.hidden = true;

    const col = document.createElement('div');
    col.className = 'col s-2 m-2 d-flex justify-content-center';

    const cardBody = document.createElement('div');
    cardBody.className = 'card-body tag-card mw-100 p-2';

    const h6 = document.createElement('h6');
    h6.className = 'card-title fw-bold border-dark';
    h6.id = 'tag-category-text-' + id;
    h6.textContent = String(tagCategoryText ?? '');

    const p = document.createElement('p');
    p.className = 'card-subtitle mb-1';
    p.id = 'tag-name-text-' + id;
    p.textContent = String(tagName ?? '');

    cardBody.append(h6, p);

    const editBtn = document.createElement('button');
    editBtn.type = 'button';
    editBtn.className = 'edit-button d-flex align-items-center nav-link px-2 d-flex justify-content-center';
    editBtn.setAttribute('data-bs-toggle', 'modal');
    editBtn.setAttribute('data-bs-target', '#addTagModal');
    editBtn.addEventListener('click', () => {
        editTag(id);
    });
    const editIcon = document.createElement('i');
    editIcon.className = 'bi bi-pencil align-middle h4';
    editBtn.appendChild(editIcon);

    const delBtn = document.createElement('button');
    delBtn.type = 'button';
    delBtn.className = 'cancel-button d-flex align-items-center nav-link px-2 d-flex justify-content-center';
    delBtn.addEventListener('click', () => {
        deleteTag(id);
    });
    const delIcon = document.createElement('i');
    delIcon.className = 'bi bi-trash align-middle h4';
    delBtn.appendChild(delIcon);

    col.append(cardBody, editBtn, delBtn);
    root.append(catInput, nameInput, col);

    document.getElementById('tags').appendChild(root);
};

function checkTagName() {
    const { checkName } = routesFromForm();
    if (!checkName) {
        return;
    }
    if (checkTagNamePending) {
        checkTagNamePending.abort();
    }
    const xhr = $.ajax({
        type: 'GET',
        url: checkName,
        data: {
            category: $tagCategory().val(),
            name: $('#tag-name').val(),
            content_locale: contributionContentLocale(),
        },
        success: function (data) {
            const n = Number(data);
            const hasMatches = Number.isFinite(n) && n > 0;
            if (hasMatches) {
                $('#tag-name-warning').prop('hidden', true);
            } else {
                $('#tag-name-warning').prop('hidden', false);
            }
        },
        error: function (_jq, textStatus) {
            if (textStatus === 'abort') {
                return;
            }
            $('#tag-name-warning').prop('hidden', false);
        },
        complete: function () {
            if (checkTagNamePending === xhr) {
                checkTagNamePending = null;
            }
        },
    });
    checkTagNamePending = xhr;
}

const TYPEAHEAD_INIT_MAX_ATTEMPTS = 100;

function initTagAutocomplete(attempt) {
    const n = attempt === undefined ? 0 : attempt;
    if (typeof $.fn.modernTypeahead === 'undefined') {
        if (n >= TYPEAHEAD_INIT_MAX_ATTEMPTS) {
            return;
        }
        setTimeout(function () {
            initTagAutocomplete(n + 1);
        }, 50);
        return;
    }
    const { autocomplete } = routesFromForm();
    if (!autocomplete) {
        return;
    }
    $('#tag-name').modernTypeahead({
        source: function (query, process) {
            return $.get(autocomplete, {
                query: query,
                category: $tagCategory().find(':selected').val(),
                content_locale: contributionContentLocale(),
            })
                .done(function (data) {
                    if (!Array.isArray(data)) {
                        process([]);
                        return;
                    }
                    process(data);
                })
                .fail(function () {
                    process([]);
                });
        },
        minLength: 1,
        delay: 300,
    });
}

$(function () {
    if (!getItemCreateForm()) {
        return;
    }
    initTagAutocomplete(0);

    const modal = $('#addTagModal');
    modal.on('click', '#save-tag-button', function (e) {
        e.preventDefault();
        saveTag();
    });
    modal.on('click', '#update-tag-button', function (e) {
        e.preventDefault();
        updateTag();
    });
    modal.on('change', 'select[name="tag-category"]', checkIfCategoryIsEmpty);
    modal.on('change', '#tag-name', checkTagName);
    $(document).on('change', '#contribution_content_locale', function () {
        checkTagName();
    });

    modal.on('hidden.bs.modal', function () {
        clearTagModalValidation();
        $tagCategory().val('').trigger('change');
        $('#tag-name').val('');

        $('#save-tag-button').prop('hidden', false);
        $('#update-tag-button').prop('hidden', true);
        $('#tag-name').prop('disabled', true);
        $('#tag-name-warning').prop('hidden', true);
    });
});
