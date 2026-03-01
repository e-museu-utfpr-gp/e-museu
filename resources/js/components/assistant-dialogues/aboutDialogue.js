export function getAboutDialogues(t) {
    return [
        { id: 1, text: t('assistant.greeting'), choices: [{ text: t('assistant.show_options'), nextId: 2 }] },
        {
            id: 2,
            text: t('assistant.what_help'),
            choices: [
                { text: t('assistant.about_page'), nextId: 3 },
                { text: t('assistant.go_home'), url: '/' },
                { text: t('assistant.explore_items'), url: 'items' },
                { text: t('assistant.contribute'), url: 'items/create' },
                { text: t('assistant.contact'), nextId: 4 },
            ],
        },
        {
            id: 3,
            text: t('assistant.about.page_intro'),
            choices: [{ text: t('assistant.back_to_options'), nextId: 2 }],
        },
        {
            id: 4,
            text: t('assistant.contact_email'),
            choices: [{ text: t('assistant.back_to_options'), nextId: 2 }],
        },
    ];
}
