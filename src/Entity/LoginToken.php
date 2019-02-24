<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity()
 * @ORM\Table(name="login_tokens")
 */
class LoginToken {
    /**
     * @ORM\Column(type="uuid")
     * @ORM\Id()
     *
     * @var UuidInterface
     */
    private $id;

    /**
     * @ORM\JoinColumn(nullable=false)
     * @ORM\ManyToOne(targetEntity="User", inversedBy="loginTokens")
     *
     * @var User
     */
    private $user;

    /**
     * @ORM\Column(type="inet")
     *
     * @var string|null
     */
    private $ip;

    /**
     * @ORM\Column(type="text", nullable=true)
     *
     * @var string|null
     */
    private $userAgent;

    /**
     * @ORM\Column(type="datetimetz")
     *
     * @var \DateTime
     */
    private $timestamp;

    /**
     * @ORM\Column(type="datetimetz")
     *
     * @var \DateTime
     */
    private $lastSeen;

    public function __construct(User $user, string $ip, ?string $userAgent) {
        $this->user = $user;
        $this->ip = $ip;
        $this->userAgent = $userAgent;
    }

    public function getId(): UuidInterface {
        return $this->id;
    }

    public function getIp(): ?string {
        return $this->ip;
    }

    public function setIp(?string $ip): void {
        $this->ip = $ip;
    }

    public function getUserAgent(): ?string {
        return $this->userAgent;
    }

    public function setUserAgent(?string $userAgent): void {
        $this->userAgent = $userAgent;
    }

    public function getTimestamp(): \DateTime {
        return $this->timestamp;
    }

    public function getLastSeen(): \DateTime {
        return $this->lastSeen;
    }

    public function updateLastSeen(): void {
        $this->lastSeen = new \DateTime('@'.time());
    }
}
