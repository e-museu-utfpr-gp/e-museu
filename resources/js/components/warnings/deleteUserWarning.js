$(document).ready(function() {
    $(".deleteUserButton").click(function() {
        var confirmation = confirm("Tem certeza que deseja excluir o administrador?");

        if (!confirmation)
            event.preventDefault();
    });
});
