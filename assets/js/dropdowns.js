'use strict';

import $ from 'jquery';

const KEY_UP = 38;
const KEY_DOWN = 40;
const KEY_ESC = 27;

const FOCUSABLE_ELEMENTS = `> .dropdown__toggle,
> .dropdown__menu a[href],
> .dropdown__menu button:not([disabled])
> .dropdown__menu input:not([disabled])`;

function toggle($dropdown) {
    // close all other dropdowns
    toggleAttributes($('.dropdown--expanded').not($dropdown));

    // toggle the current dropdown
    toggleAttributes($dropdown);

    $(document).off('keydown', globalKeyDownHandler);
    $(document).off('click', globalHtmlClickHandler);

    if ($dropdown.hasClass('dropdown--expanded')) {
        $(document).on('keydown', globalKeyDownHandler);
        $(document).one('click', globalHtmlClickHandler);
    }
}

function toggleAttributes($dropdowns) {
    $dropdowns.each(function (i, el) {
        const $dropdown = $(el);
        const expanded = !$dropdown.hasClass('dropdown--expanded');

        $dropdown
            .toggleClass('dropdown--expanded')
            .toggleClass('expanded') // BC
            .find('.dropdown__toggle')
            .attr('aria-expanded', expanded);
    });
}

function moveInList($dropdown, amount) {
    const $elements = $dropdown.find(FOCUSABLE_ELEMENTS);
    let i = $elements.index($(':focus')) + amount;

    if (i < 0) {
        i = $elements.length - 1;
    } else if (i < 0) {
        i = 0;
    }

    $elements.get(i).focus();
}

function globalKeyDownHandler(event) {
    if (event.metaKey || event.ctrlKey || event.altKey) {
        return;
    }

    const $dropdown = $('.dropdown--expanded');

    switch (event.which) {
    case KEY_ESC:
        event.originalEvent.preventDefault();
        event.originalEvent.stopPropagation();

        toggle($dropdown);

        // give focus back to toggle
        $dropdown.find('> .dropdown__toggle').first().focus();

        break;
    case KEY_DOWN:
        event.originalEvent.preventDefault();
        event.originalEvent.stopPropagation();
        moveInList($dropdown, 1);

        break;
    case KEY_UP:
        event.originalEvent.preventDefault();
        event.originalEvent.stopPropagation();
        moveInList($dropdown, -1);

        break;
    }
}

function globalHtmlClickHandler() {
    toggle($('.dropdown--expanded'));
}

// init

$('.dropdown').addClass('dropdown-container').addClass('js'); // BC
$('.dropdown__toggle').attr('aria-haspopup', true).attr('aria-expanded', false);

$(document).on('click', '.dropdown__toggle', function (event) {
    event.stopPropagation();

    toggle($(this).parent('.dropdown'));

    return false;
});
