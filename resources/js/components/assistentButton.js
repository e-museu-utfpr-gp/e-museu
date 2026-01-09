$('#assistent-button').on('click', function() {
    this.style.transition = 'transform 0.1s ease';
    this.style.transform = 'scale(0.9)';

    setTimeout(() => {
        this.style.transition = 'transform 0.1s ease';
        this.style.transform = 'scale(1.1)';

        setTimeout(() => {
            this.style.transition = 'transform 0.1s ease';
            this.style.transform = 'scale(1)';
        }, 100);
    }, 100);
});
