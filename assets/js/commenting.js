'use strict';

import $ from 'jquery';
import Translator from 'bazinga-translator';

// hide open forms (they're initially visible for non-js users)
$('.comment__form-container').hide();

$(document).on('click', '.comment__reply-link', function (event) {
    event.preventDefault();

    const $comment = $(this).closest('.comment');
    const $formContainer = $comment.find('> .comment__form-container');
    const $existingForm = $formContainer.find('.comment-form');

    // remove existing error messages
    $formContainer.find('.comment__form-error-alert').remove();

    if ($existingForm.length > 0) {
        // the form already exists, so just hide/unhide it as necessary
        $formContainer.toggle();
    } else {
        const url = $(this).data('form-url');

        // opacity indicates loading
        $(this).css('opacity', '0.5');

        $.ajax({url: url, dataType: 'html'}).done(formHtml => {
            $formContainer.prepend(formHtml);
        }).fail(() => $(() => {
            const error = Translator.trans('comments.form_load_error');
            $formContainer.prepend(`<div class="alert alert--bad comment__form-error-alert"><p>${error}</p></div>`);
        })).always(() => {
            $(this).css('opacity', 'unset');
        });
    }
});
