import $ from 'jquery';

$(document).ready(function () {
    const modal = document.querySelector('[data-image-modal]');
    if (!modal) {
        return;
    }

    const modalImg = modal.querySelector('[data-image-modal-img]');
    const captionText = modal.querySelector('[data-image-modal-caption]');

    function closeModal() {
        modal.style.display = 'none';
        if (modalImg) {
            modalImg.style.transform = '';
        }
    }

    document.addEventListener('click', function (e) {
        const img = e.target.closest('.clickable-image');
        if (!img || !modalImg) {
            return;
        }
        modal.style.display = 'block';
        modalImg.src = img.src;
        const imgTransform = window.getComputedStyle(img).transform;
        modalImg.style.transform = imgTransform === 'none' ? '' : imgTransform;
        if (captionText) {
            captionText.textContent = img.alt || '';
        }
    });

    const closeBtn = modal.querySelector('[data-image-modal-close]');
    if (closeBtn) {
        closeBtn.addEventListener('click', closeModal);
    }

    modal.addEventListener('click', function (e) {
        if (e.target === modal) {
            closeModal();
        }
    });
});
