document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('form.admin-search-form').forEach(function (form) {
        var searchColumn = form.querySelector('.admin-search-column');
        var searchText = form.querySelector('.admin-search-text');
        if (!searchColumn || !searchText) {
            return;
        }
        var searchBoolean = form.querySelector('.admin-search-boolean');
        var searchSelect = form.querySelector('.admin-search-select');
        var booleanColumns = (searchColumn.getAttribute('data-boolean-columns') || '')
            .split(',')
            .map(function (s) {
                return s.trim();
            })
            .filter(Boolean);
        var selectColumns = (searchColumn.getAttribute('data-select-columns') || '')
            .split(',')
            .map(function (s) {
                return s.trim();
            })
            .filter(Boolean);

        function toggleSearchInput() {
            var col = searchColumn.value;
            var isBoolean = searchBoolean && booleanColumns.indexOf(col) !== -1;
            var isSelect = searchSelect && selectColumns.indexOf(col) !== -1;

            if (isSelect) {
                searchText.style.display = 'none';
                searchText.disabled = true;
                searchText.removeAttribute('required');
                if (searchBoolean) {
                    searchBoolean.style.display = 'none';
                    searchBoolean.disabled = true;
                    searchBoolean.removeAttribute('required');
                }
                searchSelect.style.display = '';
                searchSelect.disabled = false;
                searchSelect.removeAttribute('required');
            } else if (isBoolean) {
                searchText.style.display = 'none';
                searchText.disabled = true;
                searchText.removeAttribute('required');
                if (searchSelect) {
                    searchSelect.style.display = 'none';
                    searchSelect.disabled = true;
                    searchSelect.removeAttribute('required');
                }
                searchBoolean.style.display = '';
                searchBoolean.disabled = false;
                searchBoolean.setAttribute('required', 'required');
            } else {
                if (searchSelect) {
                    searchSelect.style.display = 'none';
                    searchSelect.disabled = true;
                    searchSelect.removeAttribute('required');
                }
                if (searchBoolean) {
                    searchBoolean.style.display = 'none';
                    searchBoolean.disabled = true;
                    searchBoolean.removeAttribute('required');
                }
                searchText.style.display = '';
                searchText.disabled = false;
                searchText.setAttribute('required', 'required');
            }
        }

        searchColumn.addEventListener('change', toggleSearchInput);
        toggleSearchInput();

        // H2: numeric/select columns with an empty value must not submit `search_column` (avoids URLs that
        // look like a location filter while the backend applies no filter).
        form.addEventListener('submit', function () {
            var col = searchColumn.value;
            var isSelect = searchSelect && selectColumns.indexOf(col) !== -1;
            if (isSelect && searchSelect && !searchSelect.disabled && searchSelect.value === '') {
                searchColumn.removeAttribute('name');
                searchSelect.removeAttribute('name');
            }
        });
    });
});
