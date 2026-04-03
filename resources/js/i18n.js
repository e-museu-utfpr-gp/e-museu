import i18next from 'i18next';

/**
 * Map `<html lang>` (from `app()->getLocale()`) to a client bundle key.
 * To add a UI language: add `lang/js/{code}.json`, register it in `bundleLoaders`, and ship matching `lang/{code}/` for Blade.
 */
const localeFromDocument = () => {
    const lang = document.documentElement?.getAttribute?.('lang') || '';
    const normalized = lang.replace(/-/g, '_');
    if (normalized.toLowerCase().startsWith('pt')) {
        return 'pt_BR';
    }
    if (normalized.toLowerCase().startsWith('en')) {
        return 'en';
    }
    return 'pt_BR';
};

const lng = localeFromDocument();

const bundleLoaders = {
    en: () => import('../../lang/js/en.json'),
    pt_BR: () => import('../../lang/js/pt_BR.json'),
};

const defaultLoader = bundleLoaders.pt_BR;

let translation;
try {
    const loader = bundleLoaders[lng] ?? defaultLoader;
    translation = (await loader()).default;
} catch {
    translation = (await defaultLoader()).default;
}

await i18next.init({
    lng,
    fallbackLng: lng,
    resources: {
        [lng]: { translation },
    },
});

export default i18next;
