'use strict';

import $ from 'jquery';

const SEARCH_ELEMENT_PREFIX = '.sidebar__search';

// To get the host we could rely on the location
// object only, but it would break search when
// accessing the website from Tor.
function getSearchHost () {
  // So we get it from a template hidden input element.
  const siteURL = $(SEARCH_ELEMENT_PREFIX + '-site').val();

  // This fallback should never have to be used
  // but doesn't hurt to be safe.
  if (!siteURL) {
    return window.location.host;
  }

  const urlObject = new URL(siteURL);
  
  return urlObject.host;
}

function getSearchSubmitHandle ({ host, forum, inputElem }) {
  return function onSearchSubmit (e) {
    e.preventDefault();

    let query = 'site:' + host;

    // Forum filtering is optional
    if (forum) {
      query += forum;
    }

    // Query would look something like:
    // "site:example.com Search query" or
    // "site:example.com/f/forum Search query"
    query += ' ' + inputElem.val();

    const url = 'https://duckduckgo.com/?q=' + window.encodeURIComponent(query);
    window.open(url, '_blank');
  }
}

$(function () {
  const formElem = $(SEARCH_ELEMENT_PREFIX);

  // No search form detected, no need to continue
  if (!formElem.length) {
    return;
  }

  const host = getSearchHost();
  const forum = $(SEARCH_ELEMENT_PREFIX + '-forum').val();
  const inputElem = $(SEARCH_ELEMENT_PREFIX + '-query');

  // Required parameters
  if (!host || !inputElem.length) {
    return;
  }

  const onSearchSubmitHandle = getSearchSubmitHandle({
    host,
    forum,
    inputElem
  });

  formElem.submit(onSearchSubmitHandle);
});
