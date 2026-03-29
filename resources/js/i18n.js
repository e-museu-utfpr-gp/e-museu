import i18next from 'i18next';

// Any <html lang> that is not Portuguese or English falls back to pt_BR; add branches when new UI locales ship.
const localeFromDocument = () => {
    const lang = document.documentElement?.getAttribute?.('lang') || '';
    if (lang.startsWith('pt')) return 'pt_BR';
    if (lang.startsWith('en')) return 'en';
    return 'pt_BR';
};

const lng = localeFromDocument();

let translation;
try {
    translation =
        lng === 'en'
            ? (await import('../../lang/js/en.json')).default
            : (await import('../../lang/js/pt_BR.json')).default;
} catch {
    translation = (await import('../../lang/js/pt_BR.json')).default;
}

await i18next.init({
    lng,
    fallbackLng: lng,
    resources: {
        [lng]: { translation },
    },
});

export default i18next;
