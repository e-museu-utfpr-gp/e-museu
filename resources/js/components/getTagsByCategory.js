$(document).ready(function() {
    function getTags() {
        var categoryId = $('#category_id').val();
        $.ajax({
            url: "/get-tags",
            type: "GET",
            data: {category: categoryId},
            success: function(data) {
                $('#tag_id').empty();
                $.each(data, function(index, tag) {
                    $('#tag_id').append('<option value="' + tag.id + '">' + tag.name + '</option>');
                });
            }
        });
    }

    getTags();

    $('#category_id').on('change', function() {
        getTags();
    });
});
