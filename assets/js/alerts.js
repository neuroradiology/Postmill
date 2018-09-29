'use strict';

import $ from 'jquery';

$(document).on('click', '.js-dismiss-alert[data-target]', function () {
    const $alert = $('#' + $(this).data('target'));

    $alert
        .removeClass('site-alerts__alert')
        .each((i, el) => el.offsetWidth)
        .addClass('site-alerts__alert')
        .css('animation-direction', 'reverse')
        .one('animationend', function () {
            $alert.remove();
        });
});
