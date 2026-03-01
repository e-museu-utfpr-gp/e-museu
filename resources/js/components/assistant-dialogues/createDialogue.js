export function getCreateDialogues(t) {
    return [
        { id: 1, text: t('assistant.greeting'), choices: [{ text: t('assistant.show_options'), nextId: 2 }] },
        {
            id: 2,
            text: t('assistant.what_help'),
            choices: [
                { text: t('assistant.about_page'), nextId: 3 },
                { text: t('assistant.go_home'), url: '/' },
                { text: t('assistant.explore_other_items'), url: '/items' },
                { text: t('assistant.about_museum'), url: '/about' },
                { text: t('assistant.contact'), nextId: 7 },
            ],
        },
        {
            id: 3,
            text: t('assistant.create.page_intro'),
            choices: [
                { text: t('assistant.create.instructions'), nextId: 4 },
                { text: t('assistant.create.email_and_name'), nextId: 5 },
                { text: t('assistant.create.remove_item'), nextId: 6 },
                { text: t('assistant.create.other_questions'), nextId: 7 },
                { text: t('assistant.back_to_options'), nextId: 2 },
            ],
        },
        {
            id: 4,
            text: t('assistant.create.instructions_text'),
            choices: [
                { text: t('assistant.create.email_and_name'), nextId: 5 },
                { text: t('assistant.create.remove_item'), nextId: 6 },
                { text: t('assistant.create.other_questions'), nextId: 7 },
                { text: t('assistant.back_to_options'), nextId: 2 },
            ],
        },
        {
            id: 5,
            text: t('assistant.create.email_name_text'),
            choices: [
                { text: t('assistant.create.instructions'), nextId: 4 },
                { text: t('assistant.create.remove_item'), nextId: 6 },
                { text: t('assistant.create.other_questions'), nextId: 7 },
                { text: t('assistant.back_to_options'), nextId: 2 },
            ],
        },
        {
            id: 6,
            text: t('assistant.create.remove_item_text'),
            choices: [
                { text: t('assistant.create.instructions'), nextId: 4 },
                { text: t('assistant.create.email_and_name'), nextId: 5 },
                { text: t('assistant.create.other_questions'), nextId: 7 },
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
