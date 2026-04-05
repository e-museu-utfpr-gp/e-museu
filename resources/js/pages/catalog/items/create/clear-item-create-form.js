import $ from 'jquery';
import { Modal } from 'bootstrap';
import { clearItemCreateWizardStorage } from '../../../../shared/catalog/item-create-storage';
import { getItemCreateForm } from '../../../../shared/catalog/item-create-modal-helpers';
import { clearContributionFormDraft } from './contribution-form-draft';
import { resetVerificationUi } from '../../../catalog/collaborators/email-verification-code';
import { clearContributionSessionOnServer } from '../../../../shared/catalog/clear-contribution-session';

function hideContactHints() {
    const form = getItemCreateForm();
    const w = document.getElementById('email-warning');
    const s = document.getElementById('email-success');
    const p = document.getElementById('email-pending-verification');
    if (w) {
        w.hidden = true;
    }
    if (s) {
        s.hidden = true;
    }
    if (p) {
        p.hidden = true;
    }
    const net = form?.querySelector('.js-email-check-contact-network-error');
    if (net) {
        net.hidden = true;
    }
    if (form) {
        form.querySelectorAll('.js-email-internal-reserved').forEach(function (el) {
            el.hidden = true;
        });
        form.dispatchEvent(
            new CustomEvent('catalog-check-contact', {
                bubbles: true,
                detail: { internalReserved: false, exists: false },
            })
        );
    }
}

function removeWizardRows() {
    document.querySelectorAll('#tags .tag').forEach(el => el.remove());
    document.querySelectorAll('#extras .extra').forEach(el => el.remove());
    document.querySelectorAll('#components .component').forEach(el => el.remove());
}

/**
 * Sync empty-state UI for tags / extras / components without touching localStorage
 * (wizard storage was already cleared in performClear).
 */
function setWizardUiAllEmpty() {
    const kinds = ['tag', 'extra', 'component'];
    for (const kind of kinds) {
        const empty = document.getElementById(`${kind}-empty-text`);
        const full = document.getElementById(`${kind}-full-text`);
        const addBtn = document.getElementById(`add-${kind}-button`);
        const countText = document.getElementById(`${kind}-count-text`);
        if (empty) {
            empty.hidden = false;
        }
        if (full) {
            full.hidden = true;
        }
        if (addBtn) {
            addBtn.hidden = false;
        }
        if (countText) {
            countText.textContent = '0/10';
        }
    }
}

function resetWizardCountersAndUi() {
    window.tagCount = 0;
    window.tagIds = 1;
    window.extraCount = 0;
    window.extraIds = 1;
    window.componentCount = 0;
    window.componentIds = 1;
    setWizardUiAllEmpty();
}

/**
 * `form.reset()` only reverts to each control's *default* value. A restored draft (or legacy
 * `setAttribute('value', ...)`) can set that default to a date, so "clear form" must strip it.
 *
 * @param {HTMLFormElement} form
 */
function clearContributionReleaseDateDefault(form) {
    const el = form.querySelector('input[type="date"][name="date"]');
    if (!(el instanceof HTMLInputElement)) {
        return;
    }
    el.removeAttribute('value');
    el.defaultValue = '';
    el.value = '';
}

function performClear() {
    const form = getItemCreateForm();
    if (!form || !(form instanceof HTMLFormElement)) {
        return;
    }

    void clearContributionSessionOnServer(form);

    clearItemCreateWizardStorage();
    clearContributionFormDraft();

    const resetImages =
        window.__catalogItemImageUploadReset && window.__catalogItemImageUploadReset['item-create-form'];
    if (typeof resetImages === 'function') {
        resetImages();
    }

    removeWizardRows();
    form.reset();
    clearContributionReleaseDateDefault(form);
    resetWizardCountersAndUi();
    hideContactHints();
    resetVerificationUi($(form));
}

function initClearButton() {
    const form = getItemCreateForm();
    const btn = document.getElementById('item-create-clear-btn');
    const modalEl = document.getElementById('item-create-clear-modal');
    const confirmBtn = document.getElementById('item-create-clear-confirm-btn');
    if (!form || !btn || !modalEl || !confirmBtn) {
        return;
    }

    const modal = Modal.getOrCreateInstance(modalEl);

    btn.addEventListener('click', function (e) {
        e.preventDefault();
        modal.show();
    });

    confirmBtn.addEventListener('click', function () {
        performClear();
        modal.hide();
    });
}

if (getItemCreateForm()) {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initClearButton);
    } else {
        initClearButton();
    }
}
