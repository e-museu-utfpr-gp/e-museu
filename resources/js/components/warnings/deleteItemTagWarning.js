$(document).ready(function() {
    $(".deleteItemTagButton").click(function() {
        var confirmation = confirm("Tem certeza que deseja desassociar o item da etiqueta?");

        if (!confirmation)
            event.preventDefault();
    });
});
