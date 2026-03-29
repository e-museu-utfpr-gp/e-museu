document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('form.admin-search-form').forEach(function (form) {
        var searchColumn = form.querySelector('.admin-search-column');
        var searchText = form.querySelector('.admin-search-text');
        var searchBoolean = form.querySelector('.admin-search-boolean');
        if (!searchColumn || !searchText || !searchBoolean) {
            return;
        }
        var booleanColumns = (searchColumn.getAttribute('data-boolean-columns') || '')
            .split(',')
            .map(function (s) {
                return s.trim();
            })
            .filter(Boolean);

        function toggleSearchInput() {
            var col = searchColumn.value;
            var isBoolean = booleanColumns.indexOf(col) !== -1;
            if (isBoolean) {
                searchText.style.display = 'none';
                searchText.disabled = true;
                searchText.removeAttribute('required');
                searchBoolean.style.display = '';
                searchBoolean.disabled = false;
                searchBoolean.setAttribute('required', 'required');
            } else {
                searchText.style.display = '';
                searchText.disabled = false;
                searchText.setAttribute('required', 'required');
                searchBoolean.style.display = 'none';
                searchBoolean.disabled = true;
                searchBoolean.removeAttribute('required');
            }
        }

        searchColumn.addEventListener('change', toggleSearchInput);
        toggleSearchInput();
    });
});
