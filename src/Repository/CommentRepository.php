<?php

namespace App\Repository;

use App\Entity\Comment;
use App\Entity\Submission;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Persistence\ManagerRegistry;
use Pagerfanta\Adapter\DoctrineSelectableAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CommentRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Comment::class);
    }

    /**
     * @param Submission|null $submission
     * @param int|null        $id
     *
     * @return Comment
     *
     * @throws NotFoundHttpException if no such comment
     */
    public function findOneBySubmissionAndIdOr404(
        ?Submission $submission,
        ?int $id
    ): ?Comment {
        if (!$submission || !$id) {
            return null;
        }

        $comment = $this->findOneBy(['submission' => $submission, 'id' => $id]);

        if (!$comment) {
            throw new NotFoundHttpException('No such comment');
        }

        return $comment;
    }

    /**
     * @param int $page
     * @param int $maxPerPage
     *
     * @return Pagerfanta|Comment[]
     */
    public function findRecentPaginated(int $page, int $maxPerPage = 25) {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('softDeleted', false))
            ->orderBy(['timestamp' => 'DESC']);

        $pager = new Pagerfanta(new DoctrineSelectableAdapter($this, $criteria));
        $pager->setMaxPerPage($maxPerPage);
        $pager->setCurrentPage($page);

        return $pager;
    }
}
