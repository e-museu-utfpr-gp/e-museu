import $ from 'jquery';
import { resetVerificationUi } from '../../collaborators/email-verification-code';
import { clearContributionSessionOnServer } from '../../../../shared/catalog/clear-contribution-session';

/**
 * Item show: extra modal shares the public contribution verification session with item create.
 * Closing the modal without submitting should clear server-side session like “clear form” on create.
 */
function init() {
    const form = document.querySelector(
        'form[data-extra-clear-session-on-hide][data-route-clear-contribution-session]'
    );
    const modalEl = document.getElementById('addExtraModal');
    if (!(form instanceof HTMLFormElement) || !modalEl) {
        return;
    }
    modalEl.addEventListener('hidden.bs.modal', function () {
        void clearContributionSessionOnServer(form);
        resetVerificationUi($(form));
        const net = form.querySelector('.js-email-check-contact-network-error');
        if (net) {
            net.hidden = true;
        }
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
} else {
    init();
}
