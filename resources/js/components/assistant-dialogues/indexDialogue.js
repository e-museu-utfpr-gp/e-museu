export function getIndexDialogues(t) {
    return [
        { id: 1, text: t('assistant.greeting'), choices: [{ text: t('assistant.show_options'), nextId: 2 }] },
        {
            id: 2,
            text: t('assistant.what_help'),
            choices: [
                { text: t('assistant.about_page'), nextId: 3 },
                { text: t('assistant.go_home'), url: '/' },
                { text: t('assistant.contribute_question'), url: '/items/create' },
                { text: t('assistant.about_museum'), url: '/about' },
                { text: t('assistant.contact'), nextId: 7 },
            ],
        },
        {
            id: 3,
            text: t('assistant.index.page_intro'),
            choices: [
                { text: t('assistant.index.by_category'), nextId: 4 },
                { text: t('assistant.index.search'), nextId: 5 },
                { text: t('assistant.index.by_tags'), nextId: 6 },
                { text: t('assistant.back_to_options'), nextId: 2 },
            ],
        },
        {
            id: 4,
            text: t('assistant.index.categories_text'),
            choices: [
                { text: t('assistant.index.search'), nextId: 5 },
                { text: t('assistant.index.by_tags'), nextId: 6 },
                { text: t('assistant.back_to_options'), nextId: 2 },
            ],
        },
        {
            id: 5,
            text: t('assistant.index.search_text'),
            choices: [
                { text: t('assistant.index.by_category'), nextId: 4 },
                { text: t('assistant.index.by_tags'), nextId: 6 },
                { text: t('assistant.back_to_options'), nextId: 2 },
            ],
        },
        {
            id: 6,
            text: t('assistant.index.tags_text'),
            choices: [
                { text: t('assistant.index.by_category'), nextId: 4 },
                { text: t('assistant.index.search'), nextId: 5 },
                { text: t('assistant.back_to_options'), nextId: 2 },
            ],
        },
        {
            id: 7,
            text: t('assistant.contact_email'),
            choices: [{ text: t('assistant.back_to_options'), nextId: 2 }],
        },
    ];
}
