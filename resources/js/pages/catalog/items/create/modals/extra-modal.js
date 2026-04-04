import $ from 'jquery';
import {
    appendWizardOrder,
    clearExtraWizardSectionStorage,
    removeWizardOrderId,
    wizardRemoveItem,
    wizardSetItem,
} from '../../../../../shared/catalog/item-create-storage';
import { hideBootstrapModal } from '../../../../../shared/catalog/hide-bootstrap-modal';
import { getItemCreateForm, parseDataModalsI18n } from '../../../../../shared/catalog/item-create-modal-helpers';

function extraAlerts() {
    return parseDataModalsI18n().extra || { alert_required: '' };
}

function clearExtraModalValidation() {
    const el = document.getElementById('extra-modal-validation');
    if (el) {
        el.classList.add('d-none');
        el.textContent = '';
    }
}

function showExtraModalValidation(message) {
    const el = document.getElementById('extra-modal-validation');
    if (!el) {
        return;
    }
    el.textContent = message;
    el.classList.remove('d-none');
    el.focus();
}

window.extraCount = 0;
window.extraIds = 1;

function saveExtra() {
    const extraInfo = String($('#extra-info').val() ?? '').trim();

    if (extraInfo === '') {
        showExtraModalValidation(extraAlerts().alert_required || '');
        return;
    }

    const assignedId = window.extraIds;
    window.extraBuilder(extraInfo, assignedId);

    wizardSetItem('itemCreateForm', 'true');
    wizardSetItem('extra' + assignedId + 'info', extraInfo);
    appendWizardOrder('extraIdOrder', assignedId);

    window.extraCount++;
    window.extraIds++;

    wizardSetItem('extraCount', String(window.extraCount));

    clearExtraModalValidation();
    window.checkExtras();
    hideBootstrapModal('#addExtraModal');
}

function editExtra(extraId) {
    const input = $('#extra-' + extraId + ' > input').val();

    $('#extra-id').attr('value', extraId);
    $('#extra-info').val(input);

    $('#save-extra-button').prop('hidden', true);
    $('#update-extra-button').prop('hidden', false);
}

function updateExtra() {
    const extraInfo = String($('#extra-info').val() ?? '').trim();
    const extraId = $('#extra-id').val();

    if (extraInfo === '') {
        showExtraModalValidation(extraAlerts().alert_required || '');
        return;
    }

    $('#extra-info-' + extraId).val(extraInfo);
    $('#extra-info-text-' + extraId).text(extraInfo);

    wizardSetItem('extra' + extraId + 'info', extraInfo);

    clearExtraModalValidation();
    hideBootstrapModal('#addExtraModal');
}

function deleteExtra(extraId) {
    $('#extra-' + extraId).remove();
    window.extraCount--;

    wizardRemoveItem('extra' + extraId + 'info');
    removeWizardOrderId('extraIdOrder', extraId);
    wizardSetItem('extraCount', String(window.extraCount));

    window.checkExtras();
}

window.checkExtras = function checkExtras() {
    if (window.extraCount > 0) {
        $('#extra-empty-text').hide();

        if (window.extraCount > 9) {
            $('#add-extra-button').hide();
            $('#extra-full-text').prop('hidden', false);
        } else {
            $('#add-extra-button').show();
            $('#extra-full-text').prop('hidden', true);
        }
    } else {
        $('#extra-empty-text').show();
        clearExtraWizardSectionStorage();
    }

    $('#extra-count-text').text(window.extraCount + '/10');
};

window.extraBuilder = function extraBuilder(extraInfo, extraId) {
    const id = Number(extraId);
    const root = document.createElement('div');
    root.className = 'extra';
    root.id = 'extra-' + id;

    const infoInput = document.createElement('input');
    infoInput.type = 'text';
    infoInput.name = 'extras[' + id + '][info]';
    infoInput.id = 'extra-info-' + id;
    infoInput.value = String(extraInfo ?? '');
    infoInput.hidden = true;

    const col = document.createElement('div');
    col.className = 'col s-2 m-2 d-flex justify-content-center';

    const cardBody = document.createElement('div');
    cardBody.className = 'card-body tag-card mw-100 mh-100 p-2';

    const p = document.createElement('p');
    p.className = 'card-subtitle mb-1';
    p.id = 'extra-info-text-' + id;
    p.textContent = String(extraInfo ?? '');

    cardBody.appendChild(p);

    const editBtn = document.createElement('button');
    editBtn.type = 'button';
    editBtn.className = 'edit-button d-flex align-items-center nav-link px-2 d-flex justify-content-center';
    editBtn.setAttribute('data-bs-toggle', 'modal');
    editBtn.setAttribute('data-bs-target', '#addExtraModal');
    editBtn.addEventListener('click', () => {
        editExtra(id);
    });
    const editIcon = document.createElement('i');
    editIcon.className = 'bi bi-pencil align-middle h4';
    editBtn.appendChild(editIcon);

    const delBtn = document.createElement('button');
    delBtn.type = 'button';
    delBtn.className = 'cancel-button d-flex align-items-center nav-link px-2 d-flex justify-content-center';
    delBtn.addEventListener('click', () => {
        deleteExtra(id);
    });
    const delIcon = document.createElement('i');
    delIcon.className = 'bi bi-trash align-middle h4';
    delBtn.appendChild(delIcon);

    col.append(cardBody, editBtn, delBtn);
    root.append(infoInput, col);

    document.getElementById('extras').appendChild(root);
};

$(function () {
    if (!getItemCreateForm()) {
        return;
    }

    const modal = $('#addExtraModal');
    modal.on('click', '#save-extra-button', function (e) {
        e.preventDefault();
        saveExtra();
    });
    modal.on('click', '#update-extra-button', function (e) {
        e.preventDefault();
        updateExtra();
    });

    modal.on('hidden.bs.modal', function () {
        clearExtraModalValidation();
        $('#extra-info').val('');

        $('#save-extra-button').prop('hidden', false);
        $('#update-extra-button').prop('hidden', true);
    });
});
