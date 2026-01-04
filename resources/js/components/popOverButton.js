$('.info-icon').on('click', function() {
    this.style.transition = 'transform 0.1s ease';
    this.style.transform = 'scale(0.8)';

    setTimeout(() => {
        this.style.transition = 'transform 0.1s ease';
        this.style.transform = 'scale(1.2)';

        setTimeout(() => {
            this.style.transition = 'transform 0.1s ease';
            this.style.transform = 'scale(1)';
        }, 100);
    }, 100);
});

const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]')
const popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl))

$(document).ready(function() {
    const popover = new bootstrap.Popover('.info-icon', {
    trigger: 'focus'
    });
});
