Deprecated CSS classes
===

From time to time, Postmill's HTML is cleaned up and things are renamed or
otherwise changed around. In order to maintain backward compatibility with
themes, we may deprecate classes/IDs/particular HTML elements, but still keep
them in the code for some time longer while we wait for authors to update their
themes. This document describes the CSS classes that are on the chopping block.

## To be removed in September 2018

Comment classes have finally being updated to follow BEM conventions:

* `.comment-top-level` -- use `.comment--top-level` instead
* `.comment-level-*` classes were deprecated in favour of the `data-level`
  attribute -- access it via the `attr()` CSS function. The `.comment--nested`
  class was added for matching nested comments.
* `.comment-soft-deleted` -- use `.comment--soft-deleted` instead
* `.comment-row` -- use `.comment__row` instead. Note: due to an error, there
  was always a duplicate `.comment-row` class that wraps an input row in the
  comment form. As this isn't part of the 'comment' component, it remains
  unaffected.
* `.comment-inner` -- use `.comment__main` instead
* `.comment-info` -- use `.comment__info` instead
* `.comment-timestamp` -- use `.comment__timestamp` instead
* `.comment-user` -- use `.comment__author` instead
* `.comment-info-edited-at` -- use `.comment__edited-at` instead
* `.comment-info-moderated` -- use `.comment__moderated` instead
* `.comment-body` -- use `.comment__body` instead
* `.comment-nav` -- use `.comment__nav` instead
* `.comment-replies` -- use `.comment__replies` instead
* `.comment-nav-reply` -- use the nested `.comment__reply-link` instead

## Removed in May 2018

The following classes have been **removed** with no deprecation period:

* `.comment-nav-permalink` -- use the nested `.comment__permalink` instead
* `.comment-nav-parent` -- use the nested `.comment__parent-link` instead
* `.comment-nav-softdelete` -- use the nested `.comment__soft-delete-button`
  instead
* `.comment-nav-delete-thread` -- use the nested
  `.comment__thread-delete-button` instead
* `.comment-nav-edit` -- use the nested `.comment__edit-link` instead. By a
  mistake, this class also applied to the `<li>` element of the IP ban link.
  That link now has its own `.comment__ip-ban-link` class.

## Removed in April 2018

* `.submission-meta` replaces the following class names:
    * `.sidebar__section--submission-meta`
    * `.sidebar-submission-meta`
* `.submission-meta__vote-stats` replaces `.vote-stats`.
* `.submission-meta__vote-total` replaces `.vote-total`.

## Removed in January 2018

* `.active`, which is applied to the active list element in `.submission-sort`
  and `.forum-list-view-selector`, will be removed. These navs now have the
  `.tabs` class, and active elements are indicated with `.tabs__tab--active`
  (for list elements) and `.tabs__link--active` (for links). Use these classes
  to target active elements instead.
