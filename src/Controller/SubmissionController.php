<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Forum;
use App\Entity\ForumLogSubmissionDeletion;
use App\Entity\ForumLogSubmissionLock;
use App\Entity\Submission;
use App\Event\EntityModifiedEvent;
use App\Events;
use App\Form\DeleteReasonType;
use App\Form\Model\SubmissionData;
use App\Form\SubmissionType;
use App\Utils\Slugger;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Entity("forum", expr="repository.findOneOrRedirectToCanonical(forum_name, 'forum_name')")
 * @Entity("submission", expr="repository.findOneBy({forum: forum, id: submission_id})")
 * @Entity("comment", expr="repository.findOneBy({submission: submission, id: comment_id})")
 */
final class SubmissionController extends AbstractController {
    /**
     * Show a submission's comment page.
     *
     * @param Forum      $forum
     * @param Submission $submission
     *
     * @return Response
     */
    public function submission(Forum $forum, Submission $submission) {
        return $this->render('submission/submission.html.twig', [
            'forum' => $forum,
            'submission' => $submission,
        ]);
    }

    public function submissionJson(Forum $forum, Submission $submission) {
        return $this->json($submission, 200, [], [
            'groups' => ['submission:read', 'abbreviated_relations', 'submission:read:non-api'],
        ]);
    }

    /**
     * Show a single comment and its replies.
     *
     * @param Forum      $forum
     * @param Submission $submission
     * @param Comment    $comment
     *
     * @return Response
     */
    public function commentPermalink(
        Forum $forum,
        Submission $submission,
        Comment $comment
    ) {
        return $this->render('submission/comment.html.twig', [
            'comment' => $comment,
            'forum' => $forum,
            'submission' => $submission,
        ]);
    }

    /**
     * @Entity("submission", expr="repository.find(id)")
     *
     * @param Submission $submission
     *
     * @return Response
     */
    public function shortcut(Submission $submission) {
        return $this->redirectToRoute('submission', [
            'forum_name' => $submission->getForum()->getName(),
            'submission_id' => $submission->getId(),
            'slug' => Slugger::slugify($submission->getTitle()),
        ]);
    }

    /**
     * Create a new submission.
     *
     * @IsGranted("ROLE_USER")
     *
     * @param Forum|null               $forum
     * @param EntityManager            $em
     * @param Request                  $request
     * @param EventDispatcherInterface $dispatcher
     *
     * @return Response
     */
    public function submit(
        Forum $forum = null,
        EntityManager $em,
        Request $request,
        EventDispatcherInterface $dispatcher
    ) {
        $data = new SubmissionData($forum);

        $form = $this->createForm(SubmissionType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $submission = $data->toSubmission($this->getUser(), $request->getClientIp());

            $em->persist($submission);
            $em->flush();

            $dispatcher->dispatch(Events::NEW_SUBMISSION, new GenericEvent($submission));

            return $this->redirectToRoute('submission', [
                'forum_name' => $submission->getForum()->getName(),
                'submission_id' => $submission->getId(),
                'slug' => Slugger::slugify($submission->getTitle()),
            ]);
        }

        return $this->render('submission/create.html.twig', [
            'form' => $form->createView(),
            'forum' => $forum,
        ]);
    }

    /**
     * @IsGranted("ROLE_USER")
     * @IsGranted("edit", subject="submission", statusCode=403)
     *
     * @param Forum                    $forum
     * @param Submission               $submission
     * @param EntityManager            $em
     * @param Request                  $request
     * @param EventDispatcherInterface $dispatcher
     *
     * @return Response
     */
    public function editSubmission(
        Forum $forum,
        Submission $submission,
        EntityManager $em,
        Request $request,
        EventDispatcherInterface $dispatcher
    ) {
        $data = SubmissionData::createFromSubmission($submission);

        $form = $this->createForm(SubmissionType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $before = clone $submission;
            $data->updateSubmission($submission, $this->getUser());

            $em->flush();

            $this->addFlash('notice', 'flash.submission_edited');

            $event = new EntityModifiedEvent($before, $submission);
            $dispatcher->dispatch(Events::EDIT_SUBMISSION, $event);

            return $this->redirectToRoute('submission', [
                'forum_name' => $forum->getName(),
                'submission_id' => $submission->getId(),
                'slug' => Slugger::slugify($submission->getTitle()),
            ]);
        }

        return $this->render('submission/edit.html.twig', [
            'form' => $form->createView(),
            'forum' => $forum,
            'submission' => $submission,
        ]);
    }

    /**
     * @IsGranted("ROLE_USER")
     * @IsGranted("delete_with_reason", subject="submission", statusCode=403)
     *
     * @param Request       $request
     * @param EntityManager $em
     * @param Forum         $forum
     * @param Submission    $submission
     *
     * @return Response
     */
    public function deleteWithReason(Request $request, EntityManager $em, Forum $forum, Submission $submission) {
        $form = $this->createForm(DeleteReasonType::class, []);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->refresh($submission);
            $em->remove($submission);

            $forum->addLogEntry(new ForumLogSubmissionDeletion(
                $submission,
                $this->getUser(),
                $form->getData()['reason']
            ));

            $em->flush();

            $this->addFlash('notice', 'flash.submission_deleted');

            return $this->redirectToRoute('forum', [
                'forum_name' => $forum->getName(),
            ]);
        }

        return $this->render('submission/delete_with_reason.html.twig', [
            'form' => $form->createView(),
            'forum' => $forum,
            'submission' => $submission,
        ]);
    }

    /**
     * @IsGranted("ROLE_USER")
     * @IsGranted("delete_immediately", subject="submission", statusCode=403)
     *
     * @param Request       $request
     * @param EntityManager $em
     * @param Forum         $forum
     * @param Submission    $submission
     *
     * @return Response
     */
    public function deleteImmediately(Request $request, EntityManager $em, Forum $forum, Submission $submission) {
        $this->validateCsrf('delete_submission', $request->request->get('token'));

        $em->refresh($submission);
        $em->remove($submission);
        $em->flush();

        $this->addFlash('notice', 'flash.submission_deleted');

        if ($request->headers->has('Referer')) {
            return $this->redirect($request->headers->get('Referer'));
        }

        return $this->redirectToRoute('forum', ['forum_name' => $forum->getName()]);
    }

    /**
     * @IsGranted("ROLE_USER")
     * @IsGranted("moderator", subject="forum", statusCode=403)
     *
     * @param EntityManager $em
     * @param Request       $request
     * @param Forum         $forum
     * @param Submission    $submission
     * @param bool          $lock
     *
     * @return Response
     */
    public function lock(
        EntityManager $em,
        Request $request,
        Forum $forum,
        Submission $submission,
        bool $lock
    ) {
        $this->validateCsrf('lock', $request->request->get('token'));

        $submission->setLocked($lock);

        $em->persist(new ForumLogSubmissionLock($submission, $this->getUser(), $lock));
        $em->flush();

        if ($lock) {
            $this->addFlash('success', 'flash.submission_locked');
        } else {
            $this->addFlash('success', 'flash.submission_unlocked');
        }

        if ($request->headers->has('Referer')) {
            return $this->redirect($request->headers->get('Referer'));
        }

        return $this->redirectToRoute('submission', [
            'forum_name' => $forum->getName(),
            'submission_id' => $submission->getId(),
            'slug' => Slugger::slugify($submission->getTitle()),
        ]);
    }
}
