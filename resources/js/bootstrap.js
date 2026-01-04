import 'bootstrap';
import * as bootstrap from 'bootstrap';

window.bootstrap = bootstrap;

import $ from 'jquery';
window.$ = window.jQuery = $;

$(document).ready(function() {
    $.fn.modal = function(action) {
        return this.each(function() {
            const element = this;
            let modalInstance = bootstrap.Modal.getInstance(element);
            
            if (!modalInstance) {
                modalInstance = new bootstrap.Modal(element);
            }
            
            if (action === 'hide') {
                modalInstance.hide();
            } else if (action === 'show') {
                modalInstance.show();
            } else if (action === 'toggle') {
                modalInstance.toggle();
            }
        });
    };

    $.fn.modernTypeahead = function(options) {
        return this.each(function() {
            const $input = $(this);
            const inputElement = this;

            const dropdownId = inputElement.id + '-dropdown';
            let $dropdown = $('#' + dropdownId);
            
            if ($dropdown.length === 0) {
                $dropdown = $('<ul>', {
                    id: dropdownId,
                    class: 'list-group position-absolute w-100',
                    style: 'z-index: 1055; max-height: 200px; overflow-y: auto; display: none;'
                });
                
                $input.parent().css('position', 'relative');
                $input.after($dropdown);
            }
            
            let searchTimeout;
            
            $input.on('input', function() {
                const query = $(this).val().trim();
                
                if (searchTimeout) {
                    clearTimeout(searchTimeout);
                }
                
                if (query.length < (options.minLength || 1)) {
                    $dropdown.hide().empty();
                    return;
                }
                
                searchTimeout = setTimeout(() => {
                    options.source(query, function(data) {
                        $dropdown.empty();
                        
                        if (Array.isArray(data) && data.length > 0) {
                            data.forEach(item => {
                                const value = item.name || item.value || item;
                                const label = item.name || item.label || item.value || item;
                                
                                const $item = $('<li>', {
                                    class: 'list-group-item list-group-item-action',
                                    style: 'cursor: pointer; padding: 8px 12px;',
                                    text: label,
                                    'data-value': value
                                });
                                
                                $item.on('click', function() {
                                    const selectedValue = $(this).data('value');
                                    $input.val(selectedValue).trigger('change');
                                    $dropdown.hide();
                                });
                                
                                $item.on('mouseenter', function() {
                                    $(this).addClass('active');
                                }).on('mouseleave', function() {
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
            
            $(document).on('click', function(e) {
                if (!$input.is(e.target) && !$dropdown.is(e.target) && $dropdown.has(e.target).length === 0) {
                    $dropdown.hide();
                }
            });
            
            $input.on('keydown', function(e) {
                if (e.key === 'Escape') {
                    $dropdown.hide();
                }
            });
        });
    };
});

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
