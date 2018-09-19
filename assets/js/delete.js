'use strict';

import $ from 'jquery';
import Translator from 'bazinga-translator';

$(function () {
    $('.js-confirm-comment-delete').click(function () {
        return confirm(Translator.trans('prompt.confirm_comment_delete'));
    });

    $('.js-confirm-submission-delete').click(function () {
        return confirm(Translator.trans('prompt.confirm_submission_delete'));
    });
});
