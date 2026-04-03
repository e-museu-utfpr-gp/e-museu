import $ from 'jquery';
import {
    appendWizardOrder,
    clearItemCreateWizardStorage,
    removeWizardOrderId,
} from '../../../../../shared/catalog/item-create-storage';
import { hideBootstrapModal } from '../../../../../shared/catalog/hide-bootstrap-modal';
import { getItemCreateForm, parseDataModalsI18n } from '../../../../../shared/catalog/item-create-modal-helpers';

/**
 * Resolves nested keys with dot-separated `path`. Keys that contain literal `.` in JSON are not supported.
 */
function getComponentI18n(path) {
    try {
        const parts = path.split('.');
        let obj = parseDataModalsI18n();
        for (let i = 0; i < parts.length; i++) {
            obj = obj[parts[i]];
        }
        return obj || '';
    } catch {
        return '';
    }
}

function itemsByCategoryRoute() {
    const form = getItemCreateForm();
    return form ? form.getAttribute('data-route-items-by-category') || '' : '';
}

window.componentCount = 0;
window.componentIds = 1;

let componentsLoadGeneration = 0;
let componentsAbortController = null;

async function loadComponentsByCategory(categoryId, signal) {
    const base = itemsByCategoryRoute();
    if (!base) {
        return null;
    }
    try {
        const urlObj = new URL(base, window.location.origin);
        urlObj.searchParams.set('item_category', categoryId);
        const locEl = document.getElementById('contribution_content_locale');
        const loc = locEl ? String(locEl.value || '') : '';
        if (loc) {
            urlObj.searchParams.set('content_locale', loc);
        }
        const res = await fetch(urlObj.toString(), {
            headers: { Accept: 'application/json' },
            signal,
        });
        if (!res.ok) {
            throw new Error('Request failed: ' + res.status);
        }
        const data = await res.json();
        return Array.isArray(data) ? data : [];
    } catch (e) {
        if (e.name === 'AbortError') {
            return undefined;
        }
        return null;
    }
}

async function checkIfComponentCategoryIsEmpty() {
    const categoryId = $('#component-category').find(':selected').val();
    const select = $('#component-name');

    if (componentsAbortController) {
        componentsAbortController.abort();
    }
    componentsAbortController = new AbortController();
    const signal = componentsAbortController.signal;
    const gen = ++componentsLoadGeneration;

    select.empty();
    select.append($('<option>', { value: '', text: '-', selected: true }));
    $('#component-name-warning').prop('hidden', true);
    $('#save-component-button').prop('disabled', true);

    if (!categoryId) {
        select.prop('disabled', true);
        return;
    }

    select.prop('disabled', false);

    const items = await loadComponentsByCategory(categoryId, signal);
    if (gen !== componentsLoadGeneration) {
        return;
    }
    if (items === undefined) {
        return;
    }
    if (items === null) {
        $('#component-name-warning').prop('hidden', false);
        select.prop('disabled', true);
        return;
    }

    if (items.length === 0) {
        $('#component-name-warning').prop('hidden', false);
        return;
    }

    items.forEach(function (it) {
        select.append(
            $('<option>', {
                value: it.id,
                text: it.name,
            })
        );
    });
}

function saveComponent() {
    const componentCategoryText = $('#component-category').find(':selected').text();
    const componentCategoryVal = $('#component-category').find(':selected').val();
    const componentName = $('#component-name').find(':selected').text().trim();
    const componentSelectedId = $('#component-name').find(':selected').val();

    if (componentCategoryVal === '') {
        alert(getComponentI18n('component.alert_category_required'));
        return;
    }

    if (componentSelectedId === '' || componentName === '-' || componentName === '') {
        alert(getComponentI18n('component.alert_name_required'));
        return;
    }

    $('#component-name-warning').prop('hidden', true);
    addComponentToList(componentCategoryText, componentCategoryVal, componentName, componentSelectedId);
    hideBootstrapModal('#addComponentModal');
}

function addComponentToList(componentCategoryText, componentCategoryVal, componentName, componentItemId) {
    const assignedId = window.componentIds;
    window.componentBuilder(componentCategoryText, componentCategoryVal, componentName, componentItemId, assignedId);

    sessionStorage.setItem('itemCreateForm', 'true');
    sessionStorage.setItem('component' + assignedId + 'categoryText', componentCategoryText);
    sessionStorage.setItem('component' + assignedId + 'categoryVal', componentCategoryVal);
    sessionStorage.setItem('component' + assignedId + 'name', componentName);
    sessionStorage.setItem('component' + assignedId + 'itemId', String(componentItemId));
    appendWizardOrder('componentIdOrder', assignedId);

    window.componentCount++;
    window.componentIds++;

    sessionStorage.setItem('componentCount', String(window.componentCount));

    window.checkComponents();
}

function editComponent(componentId) {
    const inputs = [];
    $('#component-' + componentId + ' > input').each(function () {
        inputs.push($(this).val());
    });

    const categoryVal = inputs[0];
    const nameVal = inputs[1];
    const itemIdVal = inputs[2] || '';

    $('#component-id').attr('value', componentId);
    $('#component-category').val(categoryVal);
    checkIfComponentCategoryIsEmpty().then(function () {
        if (itemIdVal) {
            const $opt = $('#component-name option').filter(function () {
                return String($(this).val()) === String(itemIdVal);
            });
            if ($opt.length) {
                $('#component-name').val(String(itemIdVal));
            }
        } else {
            $('#component-name option')
                .filter(function () {
                    return $(this).text().trim() === nameVal;
                })
                .prop('selected', true);
        }
        checkComponentName();
    });

    $('#save-component-button').prop('hidden', true);
    $('#update-component-button').prop('hidden', false);
}

function updateComponent() {
    const componentCategoryText = $('#component-category').find(':selected').text();
    const componentCategoryVal = $('#component-category').find(':selected').val();
    const componentName = $('#component-name').find(':selected').text().trim();
    const componentSelectedId = $('#component-name').find(':selected').val();
    const componentId = $('#component-id').val();

    if (componentCategoryVal === '') {
        alert(getComponentI18n('component.alert_category_required'));
        return;
    }

    if (componentSelectedId === '' || componentName === '-' || componentName === '') {
        alert(getComponentI18n('component.alert_name_required'));
        return;
    }

    $('#category-component-' + componentId).val(componentCategoryVal);
    $('#component-category-text-' + componentId).text(componentCategoryText);
    $('#name-component-' + componentId).val(componentName);
    $('#item-component-' + componentId).val(componentSelectedId);
    $('#component-name-text-' + componentId).text(componentName);

    sessionStorage.setItem('component' + componentId + 'categoryText', componentCategoryText);
    sessionStorage.setItem('component' + componentId + 'categoryVal', componentCategoryVal);
    sessionStorage.setItem('component' + componentId + 'name', componentName);
    sessionStorage.setItem('component' + componentId + 'itemId', String(componentSelectedId));

    hideBootstrapModal('#addComponentModal');
}

function deleteComponent(componentId) {
    $('#component-' + componentId).remove();
    window.componentCount--;

    sessionStorage.removeItem('component' + componentId + 'categoryText');
    sessionStorage.removeItem('component' + componentId + 'categoryVal');
    sessionStorage.removeItem('component' + componentId + 'name');
    sessionStorage.removeItem('component' + componentId + 'itemId');
    removeWizardOrderId('componentIdOrder', componentId);
    sessionStorage.setItem('componentCount', String(window.componentCount));

    window.checkComponents();
}

window.checkComponents = function checkComponents() {
    if (window.componentCount > 0) {
        $('#component-empty-text').hide();

        if (window.componentCount > 9) {
            $('#add-component-button').hide();
            $('#component-full-text').prop('hidden', false);
        } else {
            $('#add-component-button').show();
            $('#component-full-text').prop('hidden', true);
        }
    } else {
        $('#component-empty-text').show();
        clearItemCreateWizardStorage();
    }

    $('#component-count-text').text(window.componentCount + '/10');
};

window.componentBuilder = function componentBuilder(
    componentCategoryText,
    componentCategoryVal,
    componentName,
    componentItemId,
    componentId
) {
    const id = Number(componentId);
    const root = document.createElement('div');
    root.className = 'component';
    root.id = 'component-' + id;

    const catInput = document.createElement('input');
    catInput.type = 'text';
    catInput.name = 'components[' + id + '][category_id]';
    catInput.id = 'category-component-' + id;
    catInput.value = String(componentCategoryVal ?? '');
    catInput.hidden = true;

    const nameInput = document.createElement('input');
    nameInput.type = 'text';
    nameInput.name = 'components[' + id + '][name]';
    nameInput.id = 'name-component-' + id;
    nameInput.value = String(componentName ?? '');
    nameInput.hidden = true;

    const itemIdInput = document.createElement('input');
    itemIdInput.type = 'text';
    itemIdInput.name = 'components[' + id + '][item_id]';
    itemIdInput.id = 'item-component-' + id;
    itemIdInput.value = String(componentItemId ?? '');
    itemIdInput.hidden = true;

    const col = document.createElement('div');
    col.className = 'col s-2 m-2 d-flex justify-content-center';

    const cardBody = document.createElement('div');
    cardBody.className = 'card-body tag-card mw-100 p-2';

    const h6 = document.createElement('h6');
    h6.className = 'card-title fw-bold border-dark';
    h6.id = 'component-category-text-' + id;
    h6.textContent = String(componentCategoryText ?? '');

    const p = document.createElement('p');
    p.className = 'card-subtitle mb-1';
    p.id = 'component-name-text-' + id;
    p.textContent = String(componentName ?? '');

    cardBody.append(h6, p);

    const editBtn = document.createElement('button');
    editBtn.type = 'button';
    editBtn.className = 'edit-button d-flex align-items-center nav-link px-2 d-flex justify-content-center';
    editBtn.setAttribute('data-bs-toggle', 'modal');
    editBtn.setAttribute('data-bs-target', '#addComponentModal');
    editBtn.addEventListener('click', () => {
        editComponent(id);
    });
    const editIcon = document.createElement('i');
    editIcon.className = 'bi bi-pencil align-middle h4';
    editBtn.appendChild(editIcon);

    const delBtn = document.createElement('button');
    delBtn.type = 'button';
    delBtn.className = 'cancel-button d-flex align-items-center nav-link px-2 d-flex justify-content-center';
    delBtn.addEventListener('click', () => {
        deleteComponent(id);
    });
    const delIcon = document.createElement('i');
    delIcon.className = 'bi bi-trash align-middle h4';
    delBtn.appendChild(delIcon);

    col.append(cardBody, editBtn, delBtn);
    root.append(catInput, nameInput, itemIdInput, col);

    document.getElementById('components').appendChild(root);
};

function checkComponentName() {
    const selectedId = $('#component-name').find(':selected').val();
    if (!selectedId) {
        $('#save-component-button').prop('disabled', true);
        return;
    }
    $('#save-component-button').prop('disabled', false);
}

$(function () {
    if (!getItemCreateForm()) {
        return;
    }

    const modal = $('#addComponentModal');
    modal.on('click', '#save-component-button', function (e) {
        e.preventDefault();
        saveComponent();
    });
    modal.on('click', '#update-component-button', function (e) {
        e.preventDefault();
        updateComponent();
    });
    modal.on('change', '#component-category', function () {
        void checkIfComponentCategoryIsEmpty();
    });
    modal.on('change', '#component-name', checkComponentName);

    modal.on('hidden.bs.modal', function () {
        $('#component-category').val('');
        $('#component-name')
            .empty()
            .append($('<option>', { value: '', text: '-', selected: true }));

        $('#save-component-button').prop('hidden', false);
        $('#update-component-button').prop('hidden', true);
        $('#component-name').prop('disabled', true);
        $('#component-name-warning').prop('hidden', true);
    });
});
