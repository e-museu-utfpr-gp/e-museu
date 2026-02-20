import $ from 'jquery';

$(document).ready(function () {
    $('[data-section-item-selector]').each(function () {
        const $container = $(this);
        const sectionSelector = $container.data('section-selector') || '#section_id';
        const itemSelector = $container.data('item-selector') || '#item_id';
        const originalItemId = $container.data('original-item-id') || null;
        const getItemsUrl = $container.data('get-items-url') || '/items/by-section';

        function getItems() {
            const sectionId = $(sectionSelector).val();
            if (!sectionId) {
                $(itemSelector).empty();
                return;
            }

            $.ajax({
                url: getItemsUrl,
                type: 'GET',
                data: {
                    section: sectionId,
                },
                success: function (data) {
                    $(itemSelector).empty();
                    if (Array.isArray(data) && data.length > 0) {
                        $.each(data, function (index, item) {
                            $(itemSelector).append(
                                $('<option>', {
                                    value: item.id,
                                    text: item.name,
                                })
                            );
                        });
                    }

                    if (originalItemId) {
                        $(itemSelector).val(originalItemId);
                    }
                },
                error: function (_xhr, _status, _error) {
                    // Error loading items
                },
            });
        }

        getItems();

        $(sectionSelector).on('change', function () {
            getItems();
        });
    });
});
