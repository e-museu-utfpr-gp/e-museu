import $ from 'jquery';
import {
    clearItemCreateWizardStorage,
    nextWizardIdAfterOrder,
    readWizardOrder,
} from '../../../../shared/catalog/item-create-storage';
import { getItemCreateForm } from '../../../../shared/catalog/item-create-modal-helpers';

function readSessionFlash() {
    const form = getItemCreateForm();
    if (!form) {
        return { hasSuccess: false, hasErrors: false };
    }
    try {
        return JSON.parse(form.getAttribute('data-session-flash') || '{}');
    } catch {
        return { hasSuccess: false, hasErrors: false };
    }
}

function recoverConfirmMessage() {
    const form = getItemCreateForm();
    if (!form) {
        return '';
    }
    try {
        return JSON.parse(form.getAttribute('data-recover-confirm') || '""');
    } catch {
        return '';
    }
}

function getSessionStorage() {
    const tagCount = parseInt(sessionStorage.getItem('tagCount'), 10) || 0;
    const extraCount = parseInt(sessionStorage.getItem('extraCount'), 10) || 0;
    const componentCount = parseInt(sessionStorage.getItem('componentCount'), 10) || 0;

    if (tagCount > 0) {
        const tagOrder = readWizardOrder('tagIdOrder', tagCount);
        for (let o = 0; o < tagOrder.length; o++) {
            const id = tagOrder[o];
            const tagCategoryText = sessionStorage.getItem('tag' + id + 'categoryText');
            const tagCategoryVal = sessionStorage.getItem('tag' + id + 'categoryVal');
            const tagName = sessionStorage.getItem('tag' + id + 'name');

            window.tagBuilder(tagCategoryText, tagCategoryVal, tagName, id);
        }
        window.tagIds = nextWizardIdAfterOrder('tagIdOrder', tagCount);
        window.tagCount = tagCount;
        window.checkTags();
    }

    if (extraCount > 0) {
        const extraOrder = readWizardOrder('extraIdOrder', extraCount);
        for (let o = 0; o < extraOrder.length; o++) {
            const id = extraOrder[o];
            const extraInfo = sessionStorage.getItem('extra' + id + 'info');

            window.extraBuilder(extraInfo, id);
        }
        window.extraIds = nextWizardIdAfterOrder('extraIdOrder', extraCount);
        window.extraCount = extraCount;
        window.checkExtras();
    }

    if (componentCount > 0) {
        const componentOrder = readWizardOrder('componentIdOrder', componentCount);
        for (let o = 0; o < componentOrder.length; o++) {
            const id = componentOrder[o];
            const componentCategoryText = sessionStorage.getItem('component' + id + 'categoryText');
            const componentCategoryVal = sessionStorage.getItem('component' + id + 'categoryVal');
            const componentName = sessionStorage.getItem('component' + id + 'name');
            const componentItemId = sessionStorage.getItem('component' + id + 'itemId') || '';

            window.componentBuilder(componentCategoryText, componentCategoryVal, componentName, componentItemId, id);
        }
        window.componentIds = nextWizardIdAfterOrder('componentIdOrder', componentCount);
        window.componentCount = componentCount;
        window.checkComponents();
    }
}

function initWhenJqueryReady() {
    if (typeof window.$ === 'undefined' || typeof window.jQuery === 'undefined') {
        setTimeout(initWhenJqueryReady, 50);
        return;
    }

    $(document).ready(function () {
        const flash = readSessionFlash();

        if (flash.hasSuccess) {
            clearItemCreateWizardStorage();
        }

        if (sessionStorage.getItem('itemCreateForm') === null) {
            return;
        }

        if (flash.hasErrors) {
            getSessionStorage();
            return;
        }

        const msg = recoverConfirmMessage();
        if (msg && confirm(msg)) {
            getSessionStorage();
        }
    });
}

if (getItemCreateForm()) {
    initWhenJqueryReady();
}
