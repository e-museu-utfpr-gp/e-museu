import $ from 'jquery';

$(document).ready(function () {
    $('[data-section-item-selector]').each(function () {
        const el = this;

        const sectionSelector = el.getAttribute('data-section-selector') || '#section_id';
        const itemSelector = el.getAttribute('data-item-selector') || '#item_id';
        const getItemsUrlRaw = el.getAttribute('data-get-items-url');
        const getItemsUrl = getItemsUrlRaw != null ? String(getItemsUrlRaw).trim() : '';
        const categoryQueryKey = el.getAttribute('data-category-query-key') || 'item_category';

        const oldRaw = el.getAttribute('data-old-selected-id');
        const oldSelectedId = oldRaw !== null && oldRaw !== '' ? oldRaw : null;

        const origRaw = el.getAttribute('data-original-item-id');
        const originalItemId = origRaw !== null && origRaw !== '' ? origRaw : null;

        const useQueryItemId = el.hasAttribute('data-original-item-id');

        const loadErrorMessage =
            document.documentElement.getAttribute('data-admin-dependent-select-error') ||
            'Could not load the list. Please try again.';

        let errorHost = el.querySelector('[data-section-item-selector-error]');
        if (!errorHost) {
            errorHost = document.createElement('p');
            errorHost.setAttribute('data-section-item-selector-error', '');
            errorHost.className = 'text-danger small mb-0 mt-1';
            errorHost.setAttribute('role', 'alert');
            errorHost.hidden = true;
            el.appendChild(errorHost);
        }

        function setLoadError(visible) {
            errorHost.textContent = visible ? loadErrorMessage : '';
            errorHost.hidden = !visible;
        }

        function resolveTargetValue() {
            const oldStr = oldSelectedId !== null ? String(oldSelectedId) : '';
            const origStr = originalItemId !== null ? String(originalItemId) : '';
            let queryItemId = '';
            if (useQueryItemId) {
                try {
                    queryItemId = new URL(window.location.href).searchParams.get('id') || '';
                } catch (_e) {
                    queryItemId = '';
                }
            }
            return oldStr || origStr || queryItemId;
        }

        function $sectionField() {
            return $(el).find(sectionSelector);
        }

        function $itemField() {
            return $(el).find(itemSelector);
        }

        function getItems() {
            const itemCategoryId = $sectionField().val();
            if (!itemCategoryId) {
                $itemField()
                    .empty()
                    .append($('<option>', { value: '', text: '-' }));
                setLoadError(false);
                return;
            }

            const requestData = {};
            requestData[categoryQueryKey] = itemCategoryId;

            $.ajax({
                url: getItemsUrl,
                type: 'GET',
                data: requestData,
                dataType: 'json',
                success: function (data) {
                    setLoadError(false);
                    const $select = $itemField();
                    $select.empty().append($('<option>', { value: '', text: '-' }));
                    const rows = Array.isArray(data)
                        ? data
                        : data != null && typeof data === 'object' && Array.isArray(data.data)
                          ? data.data
                          : [];
                    if (rows.length > 0) {
                        $.each(rows, function (_index, item) {
                            if (item === null || typeof item !== 'object') {
                                return;
                            }
                            const label = String(item.name ?? item.label ?? item.id ?? '');
                            $select.append(
                                $('<option>', {
                                    value: item.id,
                                    text: label,
                                })
                            );
                        });
                    }

                    const targetVal = resolveTargetValue();
                    if (targetVal) {
                        const strVal = String(targetVal);
                        const hasOption = $select.find('option').filter(function () {
                            return $(this).val() === strVal;
                        }).length;
                        if (hasOption) {
                            $select.val(strVal);
                        }
                    }
                },
                error: function () {
                    setLoadError(true);
                    $itemField()
                        .empty()
                        .append($('<option>', { value: '', text: '-' }));
                },
            });
        }

        getItems();

        $(el).on('change.eMuseuSectionItem', sectionSelector, getItems);
    });
});
