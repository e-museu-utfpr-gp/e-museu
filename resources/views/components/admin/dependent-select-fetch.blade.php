@props([
    /**
     * Container selector holding the data-* config.
     * Example: '[data-section-item-selector][data-item-selector="#item_id"]'
     */
    'containerSelector',
    /**
     * Selected id from validation errors (old()).
     */
    'oldSelectedId' => '',
])

@once
    @push('scripts')
        <script>
            (function () {
                if (window.__setupDependentSelectFetch) return;

                /**
                 * Populates a select (item) based on another select (section/category).
                 * Configuration is read from data-* attributes on the container:
                 * - data-section-selector (required)
                 * - data-item-selector (required)
                 * - data-get-items-url (required)
                 * - data-original-item-id (optional)
                 */
                window.__setupDependentSelectFetch = function (container, opts) {
                    if (!container) return;

                    var sectionSelector = container.getAttribute('data-section-selector') || '';
                    var itemSelector = container.getAttribute('data-item-selector') || '';
                    var getItemsUrl = container.getAttribute('data-get-items-url') || '';
                    var originalItemId = container.getAttribute('data-original-item-id') || '';

                    var sectionEl = document.querySelector(sectionSelector);
                    var itemEl = document.querySelector(itemSelector);

                    if (!sectionEl || !itemEl || !getItemsUrl) return;

                    var oldSelectedId = (opts && opts.oldSelectedId ? opts.oldSelectedId : '').toString();

                    function setEmpty() {
                        itemEl.innerHTML = '';
                        var opt = document.createElement('option');
                        opt.value = '';
                        opt.textContent = '-';
                        itemEl.appendChild(opt);
                    }

                    function setItems(items) {
                        itemEl.innerHTML = '';

                        var opt = document.createElement('option');
                        opt.value = '';
                        opt.textContent = '-';
                        itemEl.appendChild(opt);

                        if (Array.isArray(items) && items.length > 0) {
                            items.forEach(function (item) {
                                var o = document.createElement('option');
                                o.value = item.id;
                                o.textContent = item.name;
                                itemEl.appendChild(o);
                            });
                        }

                        // Selection priority: old() -> original (edit) -> querystring (create flows)
                        var queryItemId = '';
                        try {
                            queryItemId = new URL(window.location.href).searchParams.get('id') || '';
                        } catch (e) {}

                        var targetValue = (oldSelectedId || originalItemId || queryItemId || '').toString();
                        if (targetValue) itemEl.value = targetValue;
                    }

                    async function getItems() {
                        var sectionId = sectionEl.value;
                        if (!sectionId) {
                            setEmpty();
                            return;
                        }

                        try {
                            var urlObj = new URL(getItemsUrl, window.location.origin);
                            urlObj.searchParams.set('item_category', sectionId);
                            var res = await fetch(urlObj.toString(), { headers: { 'Accept': 'application/json' } });
                            if (!res.ok) throw new Error('Request failed: ' + res.status);
                            var data = await res.json();
                            setItems(data);
                        } catch (e) {
                            console.error(e);
                            setEmpty();
                        }
                    }

                    sectionEl.addEventListener('change', getItems);
                    getItems();
                };
            })();
        </script>
    @endpush
@endonce

@push('scripts')
    <script>
        (function () {
            var container = document.querySelector(@json($containerSelector));
            if (!container || !window.__setupDependentSelectFetch) return;
            window.__setupDependentSelectFetch(container, { oldSelectedId: @json((string) $oldSelectedId) });
        })();
    </script>
@endpush

