<?php

namespace App\Repository;

use App\Entity\Comment;
use App\Entity\Submission;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Symfony\Component\HttpFoundation\Request;

final class SearchRepository {
    private const MAX_PER_PAGE = 50;

    private const ENTITY_TYPES = [
        Comment::class => 'comment',
        Submission::class => 'submission',
    ];

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
     * @param array  $options An array with the following options:
     *                        - `query` (string)
     *                        - `is` (array with entity class names)
     *
     * @return array
     *
     * @todo pagination, more options!
     */
    public function search(array $options): array {
        $results = [];

        foreach ($options['is'] as $entityClass) {
            foreach ($this->getResultsForEntity($options, $entityClass) as $row) {
                $results[] = $row;
            }
        }

        \usort($results, function ($a, $b) {
            return $b['search_rank'] <=> $a['search_rank'];
        });

        return \array_slice($results, 0, self::MAX_PER_PAGE);
    }

    public static function parseRequest(Request $request): ?array {
        $query = $request->query->get('q');

        if (!\is_string($query)) {
            return null;
        }

        $options = [
            'is' => [Comment::class, Submission::class],
            'query' => $query,
        ];

        return $options;
    }

    private function getResultsForEntity(array $options, string $entityClass): iterable {
        if (!isset(self::ENTITY_TYPES[$entityClass])) {
            throw new \InvalidArgumentException(sprintf(
                'non-searchable entity "%s"',
                $entityClass
            ));
        }

        $rsm = new ResultSetMappingBuilder($this->em);
        $rsm->addRootEntityFromClassMetadata($entityClass, 'e');
        $rsm->addScalarResult('entity', 'entity');
        $rsm->addScalarResult('search_rank', 'search_rank');

        $table = $this->em->getClassMetadata($entityClass)->getTableName();

        $qb = $this->em->getConnection()->createQueryBuilder()
            ->select($rsm->generateSelectClause())
            ->addSelect(":entity AS entity")
            ->addSelect("ts_rank(search_doc, plainto_tsquery(:query)) AS search_rank")
            ->from($table, 'e')
            ->where('search_doc @@ plainto_tsquery(:query)')
            ->setParameter('entity', self::ENTITY_TYPES[$entityClass])
            ->setParameter('query', $options['query'])
            ->orderBy('search_rank', 'DESC')
            ->setMaxResults(self::MAX_PER_PAGE);

        return $this->em->createNativeQuery($qb->getSQL(), $rsm)
            ->setParameters($qb->getParameters())
            ->execute();
    }
}
