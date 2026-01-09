$(document).ready(function() {
    function getItems() {
        var sectionId = $('#section_id').val();
        $.ajax({
            url: "/get-items",
            type: "GET",
            data: {section: sectionId},
            success: function(data) {
                $('#item_id').empty();
                $.each(data, function(index, item) {
                    $('#item_id').append('<option value="' + item.id + '">' + item.name + '</option>');
                });
            }
        });
    }

    getItems();

    $('#section_id').on('change', function() {
        getItems();
    });
});
