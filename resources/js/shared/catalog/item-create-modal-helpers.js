export function getItemCreateForm() {
    return document.getElementById('item-create-form');
}

export function parseDataModalsI18n() {
    const form = getItemCreateForm();
    if (!form) {
        return {};
    }
    try {
        return JSON.parse(form.getAttribute('data-modals-i18n') || '{}');
    } catch {
        return {};
    }
}
