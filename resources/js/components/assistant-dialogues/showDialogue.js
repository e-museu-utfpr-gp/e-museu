export function getShowDialogues(t) {
    const backShow = [
        { text: t('assistant.show.appearance'), nextId: 4 },
        { text: t('assistant.show.release_date'), nextId: 5 },
        { text: t('assistant.show.category'), nextId: 6 },
        { text: t('assistant.show.tags_associated'), nextId: 7 },
        { text: t('assistant.show.short_description'), nextId: 8 },
        { text: t('assistant.show.history'), nextId: 9 },
        { text: t('assistant.show.timelines'), nextId: 10 },
        { text: t('assistant.show.technical_details'), nextId: 11 },
        { text: t('assistant.show.components'), nextId: 12 },
        { text: t('assistant.show.extra_info'), nextId: 13 },
        { text: t('assistant.back_to_options'), nextId: 2 },
    ];
    const backShowAlt = omit => backShow.filter(c => c.nextId !== omit);

    return [
        { id: 1, text: t('assistant.greeting'), choices: [{ text: t('assistant.show_options'), nextId: 2 }] },
        {
            id: 2,
            text: t('assistant.what_help'),
            choices: [
                { text: t('assistant.about_page'), nextId: 3 },
                { text: t('assistant.go_home'), url: '/' },
                { text: t('assistant.explore_other_items'), url: '/items' },
                { text: t('assistant.contribute_question'), url: '/items/create' },
                { text: t('assistant.about_museum'), url: '/about' },
                { text: t('assistant.contact'), nextId: 14 },
            ],
        },
        {
            id: 3,
            text: t('assistant.show.page_intro'),
            choices: [
                { text: t('assistant.show.appearance'), nextId: 4 },
                { text: t('assistant.show.release_date'), nextId: 5 },
                { text: t('assistant.show.category'), nextId: 6 },
                { text: t('assistant.show.tags_associated'), nextId: 7 },
                { text: t('assistant.show.short_description'), nextId: 8 },
                { text: t('assistant.show.history'), nextId: 9 },
                { text: t('assistant.show.timelines'), nextId: 10 },
                { text: t('assistant.show.technical_details'), nextId: 11 },
                { text: t('assistant.show.components'), nextId: 12 },
                { text: t('assistant.show.extra_info'), nextId: 13 },
                { text: t('assistant.back_to_options'), nextId: 2 },
            ],
        },
        { id: 4, text: t('assistant.show.appearance_text'), choices: backShowAlt(4) },
        { id: 5, text: t('assistant.show.release_date_text'), choices: backShowAlt(5) },
        { id: 6, text: t('assistant.show.category_text'), choices: backShowAlt(6) },
        { id: 7, text: t('assistant.show.tags_text'), choices: backShowAlt(7) },
        { id: 8, text: t('assistant.show.short_description_text'), choices: backShowAlt(8) },
        { id: 9, text: t('assistant.show.history_text'), choices: backShowAlt(9) },
        { id: 10, text: t('assistant.show.timelines_text'), choices: backShowAlt(10) },
        { id: 11, text: t('assistant.show.technical_details_text'), choices: backShowAlt(11) },
        { id: 12, text: t('assistant.show.components_text'), choices: backShowAlt(12) },
        { id: 13, text: t('assistant.show.extra_info_text'), choices: backShowAlt(13) },
        {
            id: 14,
            text: t('assistant.contact_email'),
            choices: [{ text: t('assistant.back_to_options'), nextId: 2 }],
        },
    ];
}
