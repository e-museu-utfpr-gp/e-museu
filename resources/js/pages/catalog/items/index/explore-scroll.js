const EXPLORE_SCROLL_STORAGE_KEY = 'eMuseu.exploreMenuScroll';

document.addEventListener('DOMContentLoaded', function () {
    const options = document.querySelector('.explore-menu-options');
    const optionLinks = document.querySelectorAll('.explore-menu-option');
    const leftArrow = document.querySelector('.left-arrow');
    const rightArrow = document.querySelector('.right-arrow');

    if (!options) {
        return;
    }

    let rawScroll = localStorage.getItem(EXPLORE_SCROLL_STORAGE_KEY);
    if (rawScroll === null) {
        const legacy = localStorage.getItem('scrollPosition');
        if (legacy !== null) {
            localStorage.setItem(EXPLORE_SCROLL_STORAGE_KEY, legacy);
            localStorage.removeItem('scrollPosition');
            rawScroll = legacy;
        }
    }
    const saved = parseInt(rawScroll || '0', 10);
    if (!Number.isNaN(saved)) {
        options.scrollLeft = saved;
    }

    if (rightArrow) {
        rightArrow.addEventListener('click', function () {
            options.scrollLeft += 300;
        });
    }
    if (leftArrow) {
        leftArrow.addEventListener('click', function () {
            options.scrollLeft -= 300;
        });
    }

    optionLinks.forEach(function (el) {
        el.addEventListener('click', function () {
            localStorage.setItem(EXPLORE_SCROLL_STORAGE_KEY, String(options.scrollLeft));
        });
    });
});
