$(document).ready(function() {
    $(".deleteExtraButton").click(function() {
        var confirmation = confirm("Tem certeza que deseja excluir a curiosidade extra?");

        if (!confirmation)
            event.preventDefault();
    });
});
