import $ from 'jquery';
import * as bootstrap from 'bootstrap';

let popoverDismissListenersBound = false;
let popoverTipBridgeBound = false;

/**
 * Read Bootstrap-style delay.hide from data-bs-delay JSON on the trigger (public API), without using Popover internals.
 *
 * @param {HTMLElement} triggerEl
 * @returns {number}
 */
function hideDelayMsFromTrigger(triggerEl) {
    const raw = triggerEl.getAttribute('data-bs-delay');
    if (!raw) {
        return 0;
    }
    try {
        const parsed = JSON.parse(raw);
        return typeof parsed.hide === 'number' ? parsed.hide : 0;
    } catch {
        return 0;
    }
}

/**
 * Popover is appended to body; moving the pointer from the trigger to the bubble crosses a gap.
 * Track hover on the tip via a data attribute on the trigger so we can hide after delay without Bootstrap private fields.
 */
function bindPopoverTipHoverBridge(PopoverCtor) {
    if (popoverTipBridgeBound) {
        return;
    }
    popoverTipBridgeBound = true;

    document.addEventListener('shown.bs.popover', function (e) {
        const triggerEl = e.target;
        if (!triggerEl || triggerEl.getAttribute('data-bs-toggle') !== 'popover') {
            return;
        }
        const inst = PopoverCtor.getInstance(triggerEl);
        if (!inst || !inst.tip) {
            return;
        }
        const tip = inst.tip;
        let hideTimer = null;

        function clearHideTimer() {
            if (hideTimer !== null) {
                clearTimeout(hideTimer);
                hideTimer = null;
            }
        }

        function scheduleHideFromTip() {
            clearHideTimer();
            const delay = hideDelayMsFromTrigger(triggerEl);
            hideTimer = setTimeout(function () {
                hideTimer = null;
                const j = PopoverCtor.getInstance(triggerEl);
                if (!j || !j.tip || !j.tip.classList.contains('show')) {
                    return;
                }
                if (triggerEl.getAttribute('data-e-museu-popover-tip-hover') === '1') {
                    return;
                }
                if (triggerEl === document.activeElement || triggerEl.contains(document.activeElement)) {
                    return;
                }
                j.hide();
            }, delay);
        }

        tip.addEventListener('mouseenter', function () {
            clearHideTimer();
            triggerEl.setAttribute('data-e-museu-popover-tip-hover', '1');
        });

        tip.addEventListener('mouseleave', function () {
            triggerEl.setAttribute('data-e-museu-popover-tip-hover', '0');
            scheduleHideFromTip();
        });
    });
}

function bindPopoverDismiss(PopoverCtor) {
    if (popoverDismissListenersBound) {
        return;
    }
    popoverDismissListenersBound = true;

    $(document)
        .off('click.eMuseuPopoverDismiss')
        .on('click.eMuseuPopoverDismiss', function (e) {
            setTimeout(function () {
                document.querySelectorAll('[data-bs-toggle="popover"]').forEach(function (triggerEl) {
                    const inst = PopoverCtor.getInstance(triggerEl);
                    if (!inst || !inst.tip || !inst.tip.classList.contains('show')) {
                        return;
                    }
                    if (triggerEl.contains(e.target)) {
                        return;
                    }
                    inst.hide();
                });
            }, 0);
        });

    $(document)
        .off('keydown.eMuseuPopoverDismiss')
        .on('keydown.eMuseuPopoverDismiss', function (e) {
            if (e.key !== 'Escape') {
                return;
            }
            document.querySelectorAll('[data-bs-toggle="popover"]').forEach(function (triggerEl) {
                const inst = PopoverCtor.getInstance(triggerEl);
                if (inst && inst.tip && inst.tip.classList.contains('show')) {
                    inst.hide();
                }
            });
        });
}

/**
 * Popover instances are only constructed with HTMLElement nodes from querySelectorAll,
 * never with selector strings (Bootstrap 5 API).
 */
function initPopoversAndIconAnimation() {
    const PopoverCtor = bootstrap.Popover;
    if (!PopoverCtor) {
        return;
    }

    bindPopoverDismiss(PopoverCtor);
    bindPopoverTipHoverBridge(PopoverCtor);

    const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
    popoverTriggerList.forEach(function (popoverTriggerEl) {
        if (popoverTriggerEl._eMuseuPopoverInstance) {
            return;
        }
        popoverTriggerEl._eMuseuPopoverInstance = new PopoverCtor(popoverTriggerEl, {
            container: 'body',
            popperConfig(defaultBsPopperConfig) {
                const modifiers = (defaultBsPopperConfig.modifiers || []).map(function (mod) {
                    if (mod.name === 'preventOverflow') {
                        return {
                            ...mod,
                            options: {
                                ...mod.options,
                                rootBoundary: 'viewport',
                                padding: 8,
                            },
                        };
                    }
                    return mod;
                });
                return { ...defaultBsPopperConfig, modifiers };
            },
        });
    });

    $('.info-icon')
        .off('click.eMuseuPopoverAnim')
        .on('click.eMuseuPopoverAnim', function () {
            const el = this;
            el.style.transition = 'transform 0.1s ease';
            el.style.transform = 'scale(0.8)';

            setTimeout(() => {
                el.style.transition = 'transform 0.1s ease';
                el.style.transform = 'scale(1.2)';

                setTimeout(() => {
                    el.style.transition = 'transform 0.1s ease';
                    el.style.transform = 'scale(1)';
                }, 100);
            }, 100);
        });
}

$(document).ready(function () {
    initPopoversAndIconAnimation();
});
