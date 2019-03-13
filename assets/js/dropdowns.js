'use strict';

import $ from 'jquery';

$('.dropdown-container').addClass('js'); // BC

$('.dropdown__toggle').each((i, el) => {
    const $el = $(el);

    $el.attr('aria-expanded', $el.hasClass('dropdown--expanded'));
});

$(document).on('click', '.dropdown__toggle', function (event) {
    event.stopPropagation();

    const container = $(this).parent('.dropdown');

    // close all other dropdowns
    $('.dropdown--expanded')
        .not(container)
        .removeClass('dropdown--expanded')
        .find('> .dropdown__toggle')
        .attr('aria-expanded', false);

    // toggle the current dropdown
    $(this)
        .attr('aria-expanded', !$(container).hasClass('dropdown--expanded'))
        .toggleClass('expanded') // BC
        .parent('.dropdown')
        .toggleClass('dropdown--expanded');

    if ($(this).hasClass('dropdown--expanded')) {
        $(window).scrollTo();
    }

    return false;
});


// Adds a global click handler that closes dropdowns when clicking on something
// that isn't a toggle.
$(document).on('click', 'html', () =>
    $('.dropdown--expanded')
        .removeClass('dropdown--expanded')
        .find('> .dropdown__toggle')
        .attr('aria-expanded', false)
);
