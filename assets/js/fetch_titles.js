'use strict';

import $ from 'jquery';
import Routing from 'fosjsrouting';

$(function () {
    $('.js-auto-fetch-submission-titles .js-fetch-title').blur(function () {
        const $receiver = $('.js-receive-title');
        const url = $(this).val().trim();

        if ($receiver.val().trim() === '' && /^https?:\/\//.test(url)) {
            $.ajax({
                url: Routing.generate('fetch_title'),
                method: 'POST',
                dataType: 'json',
                data: { url: url },
            }).done(data => {
                if ($receiver.val().trim() === '') {
                    $receiver.val(data.title);
                }
            }).fail(err => {
                console && console.log(err);
            });
        }
    });
});
