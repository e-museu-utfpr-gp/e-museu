<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function () {
        const options = document.querySelector('.explore-menu-options');
        const optionLinks = document.querySelectorAll('.explore-menu-option');
        const leftArrow = document.querySelector('.left-arrow');
        const rightArrow = document.querySelector('.right-arrow');

        if (!options) {
            console.warn('[explore-menu] .explore-menu-options não encontrada.');
            return;
        }

        const saved = parseInt(localStorage.getItem('scrollPosition') || '0', 10);
        if (!Number.isNaN(saved)) {
            options.scrollLeft = saved;
        }

        if (rightArrow) {
            rightArrow.addEventListener('click', () => {
                options.scrollLeft += 300;
            });
        }
        if (leftArrow) {
            leftArrow.addEventListener('click', () => {
                options.scrollLeft -= 300;
            });
        }

        optionLinks.forEach((el) => {
            el.addEventListener('click', () => {
                localStorage.setItem('scrollPosition', String(options.scrollLeft));
            });
        });

        // window.addEventListener('beforeunload', () => {
        //     // If you want to clear always, uncomment the next line
        //     // localStorage.removeItem('scrollPosition');
        // });
    });
</script>
