import * as bootstrap from 'bootstrap';

/**
 * Hide a Bootstrap 5 modal by selector (no jQuery plugin).
 */
export function hideBootstrapModal(selector) {
    const el = typeof selector === 'string' ? document.querySelector(selector) : selector;
    if (!el) {
        return;
    }
    const inst = bootstrap.Modal.getInstance(el);
    if (inst) {
        inst.hide();
    } else {
        bootstrap.Modal.getOrCreateInstance(el).hide();
    }
}
