$(document).ready(function() {
    $(".deleteContributionButton").click(function() {
        var confirmation = confirm("Tem certeza que deseja excluir a contribuição?");

        if (!confirmation)
            event.preventDefault();
    });
});
