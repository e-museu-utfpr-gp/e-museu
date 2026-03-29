import $ from 'jquery';
import * as bootstrap from 'bootstrap';

/**
 * Popover instances are only constructed with HTMLElement nodes from querySelectorAll,
 * never with selector strings (Bootstrap 5 API).
 */
function initPopoversAndIconAnimation() {
    const PopoverCtor = bootstrap.Popover;
    if (!PopoverCtor) {
        return;
    }

    const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
    popoverTriggerList.forEach(function (popoverTriggerEl) {
        if (popoverTriggerEl._eMuseuPopoverInstance) {
            return;
        }
        popoverTriggerEl._eMuseuPopoverInstance = new PopoverCtor(popoverTriggerEl);
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
