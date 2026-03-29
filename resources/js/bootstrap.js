import 'bootstrap';
import * as bootstrap from 'bootstrap';

window.bootstrap = bootstrap;

import $ from 'jquery';
window.$ = window.jQuery = $;

function ensureTypeaheadGlobalDocumentClose() {
    if (window.__eMuseuTypeaheadDocCloseBound) {
        return;
    }
    window.__eMuseuTypeaheadDocCloseBound = true;
    $(document).on('click.eMuseuTypeaheadClose', function (e) {
        document.querySelectorAll('.list-group.position-absolute[id$="-dropdown"]').forEach(function (dd) {
            const inputId = dd.id.replace(/-dropdown$/, '');
            const inputEl = document.getElementById(inputId);
            if (!inputEl) {
                return;
            }
            const $input = $(inputEl);
            const $dd = $(dd);
            if (!$input.is(e.target) && !$dd.is(e.target) && $dd.has(e.target).length === 0) {
                $dd.hide();
            }
        });
    });
}

$.fn.modernTypeahead = function (options) {
    ensureTypeaheadGlobalDocumentClose();
    return this.each(function () {
        const $input = $(this);
        const inputElement = this;

        if (!inputElement.id) {
            inputElement.id = 'e-museu-typeahead-' + Math.random().toString(36).slice(2, 11) + Date.now().toString(36);
        }

        const dropdownId = inputElement.id + '-dropdown';
        let $dropdown = $(document.getElementById(dropdownId));

        if ($dropdown.length === 0) {
            $dropdown = $('<ul>', {
                id: dropdownId,
                class: 'list-group position-absolute w-100',
                style: 'z-index: 1055; max-height: 200px; overflow-y: auto; display: none;',
            });

            $input.parent().css('position', 'relative');
            $input.after($dropdown);
        }

        let searchTimeout;

        $input.on('input', function () {
            const query = $(this).val().trim();

            if (searchTimeout) {
                clearTimeout(searchTimeout);
            }

            if (query.length < (options.minLength || 1)) {
                $dropdown.hide().empty();
                return;
            }

            searchTimeout = setTimeout(() => {
                options.source(query, function (data) {
                    $dropdown.empty();

                    if (Array.isArray(data) && data.length > 0) {
                        data.forEach(item => {
                            let labelText;
                            let valueStr;
                            if (item !== null && typeof item === 'object') {
                                labelText = String(
                                    item.label !== undefined && item.label !== null
                                        ? item.label
                                        : item.name !== undefined && item.name !== null
                                          ? item.name
                                          : (item.value ?? '')
                                );
                                if (item.value !== undefined && item.value !== null) {
                                    valueStr = String(item.value);
                                } else if (item.name !== undefined && item.name !== null) {
                                    valueStr = String(item.name);
                                } else {
                                    valueStr = labelText;
                                }
                            } else {
                                labelText = String(item);
                                valueStr = labelText;
                            }

                            const $item = $('<li>', {
                                class: 'list-group-item list-group-item-action',
                                style: 'cursor: pointer; padding: 8px 12px;',
                            });
                            $item.text(labelText);
                            $item.attr('data-value', valueStr);

                            $item.on('click', function () {
                                const selectedValue = $(this).attr('data-value');
                                $input.val(selectedValue).trigger('change');
                                $dropdown.hide();
                            });

                            $item
                                .on('mouseenter', function () {
                                    $(this).addClass('active');
                                })
                                .on('mouseleave', function () {
                                    $(this).removeClass('active');
                                });

                            $dropdown.append($item);
                        });

                        $dropdown.show();
                    } else {
                        $dropdown.hide();
                    }
                });
            }, options.delay || 300);
        });

        $input.on('keydown', function (e) {
            if (e.key === 'Escape') {
                $dropdown.hide();
            }
        });
    });
};
