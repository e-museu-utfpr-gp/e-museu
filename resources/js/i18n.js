import i18next from 'i18next';
import ptBR from '../../lang/js/pt_BR.json';
import en from '../../lang/js/en.json';

const localeFromDocument = () => {
    const lang = document.documentElement?.getAttribute?.('lang') || '';
    if (lang.startsWith('pt')) return 'pt_BR';
    if (lang.startsWith('en')) return 'en';
    return 'pt_BR';
};

i18next.init({
    lng: localeFromDocument(),
    fallbackLng: 'pt_BR',
    resources: {
        pt_BR: { translation: ptBR },
        en: { translation: en },
    },
});

export default i18next;
