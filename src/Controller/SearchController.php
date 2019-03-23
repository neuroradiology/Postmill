<?php

namespace App\Controller;

use App\Search\SearchEngine;
use Symfony\Component\HttpFoundation\Request;

final class SearchController extends AbstractController {
    /**
     * @var bool
     */
    private $enableExternalSearch;

    public function __construct(bool $enableExternalSearch) {
        $this->enableExternalSearch = $enableExternalSearch;
    }

    public function search(Request $request, SearchEngine $search) {
        $query = $request->query->get('q');

        if (\is_string($query)) {
            $results = $search->search($search->parseQuery($query));
        }

        return $this->render('search/results.html.twig', [
            'query' => $query,
            'results' => $results ?? [],
        ]);
    }

    public function external(Request $request) {
        if (!$this->enableExternalSearch) {
            throw $this->createNotFoundException('Search is not enabled');
        }

        $host = $request->getHttpHost();

        $userQuery = $request->request->get('query');
        $forum = $request->request->get('forum');

        $site = "site:$host";

        if (isset($forum)) {
            $site .= $forum;
        }

        $finalQuery = urlencode("$site $userQuery");

        $url = 'https://duckduckgo.com/?q='.$finalQuery;

        return $this->redirect($url);
    }
}
