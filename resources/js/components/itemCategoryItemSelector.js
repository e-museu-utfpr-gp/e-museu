import $ from 'jquery';

$(document).ready(function () {
    $('[data-section-item-selector]').each(function () {
        const $container = $(this);
        const sectionSelector = $container.data('section-selector') || '#section_id';
        const itemSelector = $container.data('item-selector') || '#item_id';
        const originalItemId = $container.data('original-item-id') || null;
        const getItemsUrl = $container.data('get-items-url') || '/catalog/items/by-category';

        function getItems() {
            const itemCategoryId = $(sectionSelector).val();
            if (!itemCategoryId) {
                $(itemSelector)
                    .empty()
                    .append($('<option>', { value: '', text: '-' }));
                return;
            }

            $.ajax({
                url: getItemsUrl,
                type: 'GET',
                data: {
                    item_category: itemCategoryId,
                },
                success: function (data) {
                    const $select = $(itemSelector);
                    $select.empty().append($('<option>', { value: '', text: '-' }));
                    if (Array.isArray(data) && data.length > 0) {
                        $.each(data, function (index, item) {
                            $select.append(
                                $('<option>', {
                                    value: item.id,
                                    text: item.name,
                                })
                            );
                        });
                    }

                    if (originalItemId) {
                        $select.val(originalItemId);
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
