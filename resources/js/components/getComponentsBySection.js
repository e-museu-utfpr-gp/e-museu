$(document).ready(function() {
    function getComponents() {
        var componentSectionId = $('#component_section_id').val();
        $.ajax({
            url: "/get-items",
            type: "GET",
            data: {section: componentSectionId},
            success: function(data) {
                $('#component_id').empty();
                $.each(data, function(index, component) {
                    $('#component_id').append('<option value="' + component.id + '">' + component.name + '</option>');
                });
            }
        });
    }

    getComponents();

    $('#component_section_id').on('change', function() {
        getComponents();
    });
});
