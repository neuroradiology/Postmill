<?php

namespace App\Entity;

use App\Entity\Exception\BannedFromForumException;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SubmissionRepository")
 * @ORM\Table(name="submissions", indexes={
 *     @ORM\Index(name="submissions_ranking_id_idx", columns={"ranking", "id"})
 * })
 * @ApiResource(
 * 	attributes={
 * 		"normalization_context"={"groups"={"read", "mod:read", "admin:read"}},
 * 		"denormalization_context"={"groups"={"write", "mod:write", "admin:write"}},
 * 	}
 * )
 */
class Submission extends Votable {
    const DOWNVOTED_CUTOFF = -5;
    const NETSCORE_MULTIPLIER = 1800;
    const COMMENT_MULTIPLIER = 5000;
    const COMMENT_DOWNVOTED_MULTIPLIER = 500;
    const MAX_ADVANTAGE = 86400;
    const MAX_PENALTY = 43200;

    /**
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Id()
     * @Groups({"read"})
     *
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     * @Groups({"read", "write"})
     *
     * @var string
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"read", "write"})
     *
     * @var string
     */
    private $url;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"read", "write"})
     *
     * @var string
     */
    private $body;

    /**
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="submission",
     *     fetch="EXTRA_LAZY", cascade={"remove"})
     * @Groups({"read"})
     * @ApiSubresource()
     *
     * @var Comment[]|Collection
     */
    private $comments;

    /**
     * @ORM\Column(type="datetimetz")
     * @Groups({"read"})
     *
     * @var \DateTime
     */
    private $timestamp;

    /**
     * @ORM\JoinColumn(nullable=false)
     * @ORM\ManyToOne(targetEntity="Forum", inversedBy="submissions")
     * @Groups({"read", "write"})
     *
     * @var Forum
     */
    private $forum;

    /**
     * @ORM\JoinColumn(nullable=false)
     * @ORM\ManyToOne(targetEntity="User", inversedBy="submissions")
     * @Groups({"read"})
     * @ApiSubresource()
     *
     * @var User
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="SubmissionVote", mappedBy="submission",
     *     fetch="EXTRA_LAZY", cascade={"persist", "remove"}, orphanRemoval=true)
     * @Groups({"read"})
     *
     * @var SubmissionVote[]|Collection
     */
    private $votes;

    /**
     * @ORM\OneToMany(targetEntity="SubmissionMention", mappedBy="submission", cascade={"remove"})
     * @Groups({"read"})
     *
     * @var SubmissionMention[]|Collection
     */
    private $mentions;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"read"})
     *
     * @var string
     */
    private $image;

    /**
     * @ORM\Column(type="inet", nullable=true)
     * @Groups({"admin:read"})
     *
     * @var string|null
     */
    private $ip;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"read", "mod:write"})
     *
     * @var bool
     */
    private $sticky = false;

    /**
     * @ORM\Column(type="bigint")
     * @Groups({"admin:read"})
     *
     * @var int
     */
    private $ranking;

    /**
     * @ORM\Column(type="datetimetz", nullable=true)
     * @Groups({"admin:read"})
     *
     * @var \DateTime|null
     */
    private $editedAt;

    /**
     * @ORM\Column(type="boolean", options={"default": false})
     * @Groups({"admin:read"})
     *
     * @var bool
     */
    private $moderated = false;

    /**
     * @ORM\Column(type="smallint", options={"default": 0})
     * @Groups({"admin:read"})
     *
     * @var int
     */
    private $userFlag;

    /**
     * @ORM\Column(type="boolean", options={"default": false})
     * @Groups({"admin:read"})
     *
     * @var bool
     */
    private $locked = false;

    public function __construct(
        string $title,
        ?string $url,
        ?string $body,
        Forum $forum,
        User $user,
        ?string $ip,
        bool $sticky = false,
        int $userFlag = UserFlags::FLAG_NONE,
        \DateTime $timestamp = null
    ) {
        if ($ip !== null && !filter_var($ip, FILTER_VALIDATE_IP)) {
            throw new \InvalidArgumentException('Invalid IP address');
        }

        if ($forum->userIsBanned($user)) {
            throw new BannedFromForumException();
        }

        $this->title = $title;
        $this->url = $url;
        $this->body = $body;
        $this->forum = $forum;
        $this->user = $user;
        $this->ip = $user->isTrustedOrAdmin() ? null : $ip;
        $this->sticky = $sticky;
        $this->setUserFlag($userFlag);
        $this->timestamp = $timestamp ?: new \DateTime('@'.time());
        $this->comments = new ArrayCollection();
        $this->votes = new ArrayCollection();
        $this->vote($user, $ip, Votable::VOTE_UP);
        $this->mentions = new ArrayCollection();
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getTitle(): string {
        return $this->title;
    }

    public function setTitle(string $title) {
        $this->title = $title;
    }

    public function getUrl(): ?string {
        return $this->url;
    }

    public function setUrl(?string $url) {
        $this->url = $url;
    }

    public function getBody(): ?string {
        return $this->body;
    }

    public function setBody(?string $body) {
        $this->body = $body;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection {
        return $this->comments;
    }

    public function getCommentCount(): int {
        return \count($this->comments);
    }

    /**
     * Get top-level comments, ordered by descending net score.
     *
     * @return Comment[]
     */
    public function getTopLevelComments(): array {
        $criteria = Criteria::create();
        $criteria->where(Criteria::expr()->isNull('parent'));

        $comments = $this->comments->matching($criteria)->toArray();

        if ($comments) {
            usort($comments, [$this, 'descendingNetScoreCmp']);
        }

        return $comments;
    }

    public function addComment(Comment $comment) {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
        }

        $this->updateRanking();
    }

    public function getTimestamp(): \DateTime {
        return $this->timestamp;
    }

    public function getForum(): Forum {
        return $this->forum;
    }

    public function getUser(): User {
        return $this->user;
    }

    /**
     * {@inheritdoc}
     */
    public function getVotes(): Collection {
        return $this->votes;
    }

    /**
     * {@inheritdoc}
     */
    protected function createVote(User $user, ?string $ip, int $choice): Vote {
        return new SubmissionVote($user, $ip, $choice, $this);
    }

    /**
     * {@inheritdoc}
     */
    public function vote(User $user, ?string $ip, int $choice): void {
        if ($this->forum->userIsBanned($user)) {
            throw new BannedFromForumException();
        }

        parent::vote($user, $ip, $choice);

        $this->updateRanking();
    }

    public function addMention(User $mentioned) {
        if (
            !$mentioned->isBlocking($this->getUser()) &&
            $mentioned !== $this->getUser()
        ) {
            $mentioned->sendNotification(new SubmissionMention($mentioned, $this));
        }
    }

    public function getImage(): ?string {
        return $this->image;
    }

    public function setImage(?string $image) {
        $this->image = $image;
    }

    public function getIp(): ?string {
        return $this->ip;
    }

    public function isSticky(): bool {
        return $this->sticky;
    }

    public function setSticky(bool $sticky) {
        $this->sticky = $sticky;
    }

    /**
     * @return int
     */
    public function getRanking(): int {
        return $this->ranking;
    }

    public function updateRanking() {
        $netScore = $this->getNetScore();
        $netScoreAdvantage = $netScore * self::NETSCORE_MULTIPLIER;

        if ($netScore > self::DOWNVOTED_CUTOFF) {
            $commentAdvantage = count($this->comments) * self::COMMENT_MULTIPLIER;
        } else {
            $commentAdvantage = count($this->comments) * self::COMMENT_DOWNVOTED_MULTIPLIER;
        }

        $advantage = max(min($netScoreAdvantage + $commentAdvantage, self::MAX_ADVANTAGE), -self::MAX_PENALTY);

        $this->ranking = $this->getTimestamp()->getTimestamp() + $advantage;
    }

    public function getEditedAt(): ?\DateTime {
        return $this->editedAt;
    }

    public function setEditedAt(?\DateTime $editedAt) {
        $this->editedAt = $editedAt;
    }

    public function isModerated(): bool {
        return $this->moderated;
    }

    public function setModerated(bool $moderated) {
        $this->moderated = $moderated;
    }

    public function getUserFlag(): int {
        return $this->userFlag;
    }

    public function setUserFlag(int $userFlag) {
        if (!in_array($userFlag, UserFlags::FLAGS, true)) {
            throw new \InvalidArgumentException('Bad flag');
        }

        $this->userFlag = $userFlag;
    }

    public function isLocked(): bool {
        return $this->locked;
    }

    public function setLocked(bool $locked) {
        $this->locked = $locked;
    }
}
