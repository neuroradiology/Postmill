<?php

namespace App;

final class Events {
    public const NEW_SUBMISSION = 'postmill.new_submission';
    public const EDIT_SUBMISSION = 'postmill.edit_submission';

    public const NEW_COMMENT = 'postmill.new_comment';
    public const EDIT_COMMENT = 'postmill.edit_comment';

    public const COMMONMARK_CACHE = 'postmill.commonmark_cache';
    public const COMMONMARK_INIT = 'postmill.commonmark_init';

    private function __construct() {}
}
