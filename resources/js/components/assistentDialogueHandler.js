import $ from 'jquery';
import i18next from 'i18next';
import { getHomeDialogues } from './assistant-dialogues/homeDialogue';
import { getAboutDialogues } from './assistant-dialogues/aboutDialogue';
import { getCreateDialogues } from './assistant-dialogues/createDialogue';
import { getIndexDialogues } from './assistant-dialogues/indexDialogue';
import { getShowDialogues } from './assistant-dialogues/showDialogue';

$(document).ready(function () {
    const path = window.location.pathname;
    let getDialogues = getHomeDialogues;
    if (path === '/about') {
        getDialogues = getAboutDialogues;
    } else if (path.includes('/items/create')) {
        getDialogues = getCreateDialogues;
    } else if (path.includes('/items') && !path.includes('/create')) {
        if (path.match(/^\/items\/\d+/)) {
            getDialogues = getShowDialogues;
        } else {
            getDialogues = getIndexDialogues;
        }
    }

    const dialogues = getDialogues(i18next.t.bind(i18next));

    function displayDialogue(nodeId) {
        const node = dialogues.find(d => d.id === nodeId);
        if (!node) {
            return;
        }

        $('#dialogue').text(node.text);
        $('#choices').empty();

        node.choices.forEach(choice => {
            const $button = $('<button></button>')
                .text(choice.text)
                .addClass('choice nav-link px-2 py-1 m-1 fw-bold explore-button');

            $button.on('click', function () {
                if (choice.nextId !== undefined) {
                    displayDialogue(choice.nextId);
                } else if (choice.url) {
                    window.location.href = choice.url;
                } else {
                    $('#dialogue').text(i18next.t('assistant.goodbye'));
                    $('#choices').empty();
                }
            });
            $('#choices').append($button);
        });
    }

    displayDialogue(1);
});
