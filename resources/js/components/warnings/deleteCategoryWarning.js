$(document).ready(function() {
    $(".deleteCategoryButton").click(function() {
        var confirmation = confirm("Tem certeza que deseja excluir a categoria e todas as vezes que é referenciada em outros registros?");

        if (!confirmation)
            event.preventDefault();
    });
});
