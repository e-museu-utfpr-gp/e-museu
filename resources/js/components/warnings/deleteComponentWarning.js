$(document).ready(function() {
    $(".deleteComponentButton").click(function() {
        var confirmation = confirm("Tem certeza que deseja desassociar o item do componente?");

        if (!confirmation)
            event.preventDefault();
    });
});
