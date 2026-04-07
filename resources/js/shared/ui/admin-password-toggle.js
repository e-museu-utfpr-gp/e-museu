(function initAdminPasswordToggle() {
    if (window.__adminPasswordToggleInitialized) {
        return;
    }
    window.__adminPasswordToggleInitialized = true;

    document.addEventListener('click', function (event) {
        var button = event.target.closest('[data-password-toggle]');
        if (!button) {
            return;
        }

        var targetId = button.getAttribute('data-target');
        var input = targetId ? document.getElementById(targetId) : null;
        if (!input) {
            button.setAttribute('aria-invalid', 'true');
            return;
        }
        button.removeAttribute('aria-invalid');

        var icon = button.querySelector('i');
        var showLabel = button.getAttribute('data-label-show') || 'Show password';
        var hideLabel = button.getAttribute('data-label-hide') || 'Hide password';
        var showing = input.type === 'text';

        input.type = showing ? 'password' : 'text';

        if (icon) {
            icon.classList.toggle('bi-eye', showing);
            icon.classList.toggle('bi-eye-slash', !showing);
        }

        var nextLabel = showing ? showLabel : hideLabel;
        button.setAttribute('aria-label', nextLabel);
        button.setAttribute('title', nextLabel);
    });
})();
