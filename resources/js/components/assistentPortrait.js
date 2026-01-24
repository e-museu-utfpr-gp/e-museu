import $ from 'jquery';

$(document).ready(function() {
    $('.assistent-portrait').on('click', function() {
        const $img = $(this);
        const currentSrc = $img.attr('src');

        $img.attr('src', currentSrc.includes('/img/assistent1.png') 
            ? '/img/assistent2.png' 
            : '/img/assistent1.png');
    });
});

