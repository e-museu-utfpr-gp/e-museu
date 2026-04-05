import $ from 'jquery';

/**
 * Admin selects use enhanced-select: the visible toggle keeps the original id; the native <select> is
 * visually hidden and id is removed. Resolve the real <select> for .val(), .empty(), and change events.
 *
 * @param {Element} container
 * @param {string} selector
 * @returns {HTMLSelectElement | null}
 */
function resolveNativeSelect(container, selector) {
    const s = String(selector || '').trim();
    if (!s) {
        return null;
    }
    if (s.startsWith('#')) {
        const id = s.slice(1);
        let escaped = id;
        if (typeof CSS !== 'undefined' && typeof CSS.escape === 'function') {
            escaped = CSS.escape(id);
        }
        const hit = container.querySelector(`#${escaped}`);
        if (hit instanceof HTMLSelectElement) {
            return hit;
        }
        if (hit instanceof HTMLElement) {
            const root = hit.closest('.enhanced-select-root');
            const sel = root?.querySelector('select');
            if (sel instanceof HTMLSelectElement) {
                return sel;
            }
        }
        return null;
    }
    const elHit = container.querySelector(s);
    return elHit instanceof HTMLSelectElement ? elHit : null;
}

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

        function sectionNative() {
            return resolveNativeSelect(el, sectionSelector);
        }

        function itemNative() {
            return resolveNativeSelect(el, itemSelector);
        }

        function getItems() {
            const sectionEl = sectionNative();
            const itemEl = itemNative();
            if (!sectionEl || !itemEl) {
                return;
            }

            const itemCategoryId = $(sectionEl).val();
            if (!itemCategoryId) {
                $(itemEl)
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
                    const $select = $(itemEl);
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
                    $(itemEl)
                        .empty()
                        .append($('<option>', { value: '', text: '-' }));
                },
            });
        }

        getItems();

        const sec = sectionNative();
        if (sec) {
            $(sec).on('change.eMuseuSectionItem', getItems);
        }
    });
});
