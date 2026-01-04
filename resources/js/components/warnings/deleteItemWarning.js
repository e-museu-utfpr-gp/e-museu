$(document).ready(function() {
    $(".deleteItemButton").click(function(event) {
        var confirmation = confirm("Tem certeza que deseja excluir o item e todas as vezes que é referenciado em outros registros?");

        if (!confirmation)
            event.preventDefault();
    });
});
