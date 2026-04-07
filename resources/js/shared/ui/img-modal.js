import $ from 'jquery';

$(document).ready(function () {
    const modal = document.getElementById('myModal');
    if (!modal) {
        return;
    }

    const modalImg = document.getElementById('modal-img');
    const captionText = document.getElementById('modal-caption');

    function closeModal() {
        modal.style.display = 'none';
    }

    document.addEventListener('click', function (e) {
        const img = e.target.closest('.clickable-image');
        if (!img || !modalImg) {
            return;
        }
        modal.style.display = 'block';
        modalImg.src = img.src;
        if (captionText) {
            captionText.textContent = img.alt || '';
        }
    });

    const closeBtn = modal.querySelector('.close');
    if (closeBtn) {
        closeBtn.addEventListener('click', closeModal);
    }

    modal.addEventListener('click', function (e) {
        if (e.target === modal) {
            closeModal();
        }
    });
});
