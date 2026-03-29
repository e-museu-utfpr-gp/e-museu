import i18next from 'i18next';

// Infer i18next locale from <html lang>. Layouts should set lang from app()->getLocale() on the same response as Blade __(); otherwise JS strings and server-rendered text can diverge. Non-pt/non-en values fall back to pt_BR until more branches are added.
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
