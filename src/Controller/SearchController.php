<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class SearchController extends AbstractController {
    /**
     * @var bool
     */
    private $enableExternalSearch;

    public function __construct(bool $enableExternalSearch) {
        $this->enableExternalSearch = $enableExternalSearch;
    }

    public function external(Request $request) {
        if (!$this->enableExternalSearch) {
            return new Response();
        }

        $host = $request-> getHttpHost();

        $userQuery = $request->request->get('query');
        $forum = $request->request->get('forum');

        $site = "site:$host";

        if (isset($forum)) {
          $site .= $forum;
        }

        $finalQuery = urlencode("$site $userQuery");

        $url = "http://duckduckgo.com/?q=" . $finalQuery;

        return $this->redirect($url);
    }
}
