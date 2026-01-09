$(document).ready(function() {
    $(".deleteTagButton").click(function() {
        var confirmation = confirm("Tem certeza que deseja excluir a etiqueta e todas as vezes que é referenciada em outros registros?");

        if (!confirmation)
            event.preventDefault();
    });
});
