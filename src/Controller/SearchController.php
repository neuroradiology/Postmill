<?php

namespace App\Controller;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;

final class SearchController extends AbstractController {
    /**
     * @var bool
     */
    private $enableExternalSearch;

    public function __construct(bool $enableExternalSearch) {
        $this->enableExternalSearch = $enableExternalSearch;
    }

    public function search(Request $request, EntityManager $em) {
        $query = $request->query->get('q');

        // TODO: make a repository thing or something for this
        if (\is_string($query)) {
            $sth = $em->getConnection()->prepare(<<<'EOSQL'
    SELECT id,
        'submission' AS entity,
        ts_headline(title || ' ' || COALESCE(body, ''), plainto_tsquery(:query))
            AS headline
    FROM submissions
    WHERE search_doc @@ plainto_tsquery(:query)
UNION ALL
    SELECT id,
        'comment' AS entity,
        ts_headline(body, plain_query) AS headline
    FROM comments
    WHERE soft_deleted = FALSE AND search_doc @@ plain_query
EOSQL
        );
            $sth->bindValue(':query', $query);
            $sth->execute();

            $results = $sth->fetchAll();
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
