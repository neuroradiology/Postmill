<?php

namespace App\Search;

use App\Entity\Comment;
use App\Entity\Submission;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

final class SearchEngine {
    private const ENTITY_TYPES = ['comment', 'submission'];
    private const MAX_PER_PAGE = 25;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
    }

    /**
     * The more amazing-er cross-entity search engine.
     *
     * @param string $options An array with the following options:
     *                        - `query` (string)
     *                        - `since` (\DateTime)
     *                        - `until` (\DateTime)
     *                        - `is` (array with values 'comment', 'submission')
     *
     * @return mixed
     */
    public function search(array $options) {
        $results = [];

        if (!isset($options['query'])) {
            throw new \InvalidArgumentException('$options must contain "query" key');
        }

        if (\in_array('comment', $options['is'] ?? self::ENTITY_TYPES, true)) {
            $rsm = new ResultSetMappingBuilder($this->em);
            $rsm->addRootEntityFromClassMetadata(Comment::class, 'c');
            $rsm->addScalarResult('entity', 'entity');
            $rsm->addScalarResult('headline', 'headline');
            $rsm->addScalarResult('search_rank', 'search_rank');

            $qb = $this->em->getConnection()->createQueryBuilder()
                ->select($rsm->generateSelectClause())
                ->addSelect("'comment' AS entity")
                ->addSelect("ts_headline(body, plainto_tsquery(:query)) AS headline")
                ->addSelect("ts_rank(search_doc, plainto_tsquery(:query)) AS search_rank")
                ->from('comments', 'c')
                ->where('search_doc @@ plainto_tsquery(:query)')
                ->orderBy('search_rank', 'DESC')
                ->setParameter('query', $options['query'])
                ->setMaxResults(self::MAX_PER_PAGE + 1);

            if ($options['until'] ?? false) {
                $qb->andWhere('timestamp <= :until')
                    ->setParameter('until', $options['until'], Type::DATETIMETZ);
            }

            if ($options['since'] ?? false) {
                $qb->andWhere('timestamp >= :since')
                    ->setParameter('since', $options['since'], Type::DATETIMETZ);
            }

            $comments = $this->em->createNativeQuery($qb->getSQL(), $rsm)
                ->setParameters($qb->getParameters())
                ->execute();
        }

        if (\in_array('submission', $options['is'] ?? self::ENTITY_TYPES, true)) {
        }

        $results = \array_merge($submissions, $comments);

        \usort($results, function ($a, $b) {
            return $b->getTimestamp() <=> $a->getTimestamp();
        });

        return $results;
    }

    public static function parseQuery(string $query): array {
        return [
            'query' => $query,
            'is' => ['comment', 'submission'],
        ];
    }

    private function getResultsForEntity(string $entityClass, string $headlineExpr): array {
        $rsm = new ResultSetMappingBuilder($this->em);
        $rsm->addRootEntityFromClassMetadata($entityClass, 'e');
        $rsm->addScalarResult('headline', 'headline');
        $rsm->addScalarResult('rank', 'rank');

        $qb = $this->em->getConnection()->createQueryBuilder()
            ->select($rsm->generateSelectClause())
            ->addSelect("'submission' AS entity")
            ->addSelect("ts_headline(".
                            "title || ' ' || COALESCE(body, ''), ".
                            "plainto_tsquery(:query)".
                        ") AS headline")
            ->addSelect("ts_rank(search_doc, plainto_tsquery(:query)) AS search_rank")
            ->from('submissions', 's')
            ->where('search_doc @@ plainto_tsquery(:query)')
            ->setParameter('query', $options['query'])
            ->orderBy('search_rank', 'DESC')
            ->setMaxResults(self::MAX_PER_PAGE + 1);

        if ($options['until'] ?? false) {
            $qb->andWhere('timestamp <= :until')
                ->setParameter('until', $options['until'], Type::DATETIMETZ);
        }

        if ($options['since'] ?? false) {
            $qb->andWhere('timestamp >= :since')
                ->setParameter('since', $options['since'], Type::DATETIMETZ);
        }

        $submissions = $this->em->createNativeQuery($qb->getSQL(), $rsm)
            ->setParameters($qb->getParameters())
            ->execute();
    }
}
