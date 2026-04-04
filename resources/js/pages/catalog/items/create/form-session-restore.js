import $ from 'jquery';
import {
    clearItemCreateWizardStorage,
    ensureWizardStorageFresh,
    nextWizardIdAfterOrder,
    readWizardOrder,
    wizardGetItem,
} from '../../../../shared/catalog/item-create-storage';
import { getItemCreateForm } from '../../../../shared/catalog/item-create-modal-helpers';
import {
    clearContributionFormDraft,
    hasContributionFormDraft,
    initContributionFormDraftAutosave,
    restoreContributionFormDraft,
} from './contribution-form-draft';

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

function restoreWizardRowsFromLocal() {
    const tagCount = parseInt(wizardGetItem('tagCount') ?? '', 10) || 0;
    const extraCount = parseInt(wizardGetItem('extraCount') ?? '', 10) || 0;
    const componentCount = parseInt(wizardGetItem('componentCount') ?? '', 10) || 0;

    if (tagCount > 0) {
        const tagOrder = readWizardOrder('tagIdOrder', tagCount);
        for (let o = 0; o < tagOrder.length; o++) {
            const id = tagOrder[o];
            const tagCategoryText = wizardGetItem('tag' + id + 'categoryText');
            const tagCategoryVal = wizardGetItem('tag' + id + 'categoryVal');
            const tagName = wizardGetItem('tag' + id + 'name');

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
            const extraInfo = wizardGetItem('extra' + id + 'info');

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
            const componentCategoryText = wizardGetItem('component' + id + 'categoryText');
            const componentCategoryVal = wizardGetItem('component' + id + 'categoryVal');
            const componentName = wizardGetItem('component' + id + 'name');
            const componentItemId = wizardGetItem('component' + id + 'itemId') || '';

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
        const form = getItemCreateForm();
        initContributionFormDraftAutosave(form);

        const flash = readSessionFlash();

        if (flash.hasSuccess) {
            clearItemCreateWizardStorage();
            clearContributionFormDraft();
            return;
        }

        ensureWizardStorageFresh();

        if (flash.hasErrors) {
            restoreWizardRowsFromLocal();
            return;
        }

        const hasWizard = wizardGetItem('itemCreateForm') !== null;
        const hasDraft = hasContributionFormDraft();
        if (!hasWizard && !hasDraft) {
            return;
        }

        if (hasWizard) {
            restoreWizardRowsFromLocal();
        }
        if (hasDraft && form) {
            restoreContributionFormDraft(form);
        }
    });
}

if (getItemCreateForm()) {
    initWhenJqueryReady();
}
