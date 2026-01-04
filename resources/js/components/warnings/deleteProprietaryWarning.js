$(document).ready(function() {
    $(".deleteProprietaryButton").click(function() {
        var confirmation = confirm("Tem certeza que deseja excluir o proprietário e todas as vezes que é referenciado em outros registros?");

        if (!confirmation)
            event.preventDefault();
    });
});
