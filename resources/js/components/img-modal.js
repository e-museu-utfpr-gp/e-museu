$(document).ready(function() {
    var modal = document.getElementById("myModal");
    
    if (!modal) {
        console.warn('Image modal not found on this page');
        return;
    }

    var img = document.getElementsByClassName("clickable-image");
    var modalImg = document.getElementById("modal-img");
    var captionText = document.getElementById("modal-caption");

    for (var i = 0; i < img.length; i++) {
        img[i].addEventListener("click", function() {
            modal.style.display = "block";
            modalImg.src = this.src;
            captionText.innerHTML = this.alt;
        });
    }

    var span = document.getElementsByClassName("close")[0];

    if (span) {
        span.onclick = function() {
            modal.style.display = "none";
        }
    }

    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
});
