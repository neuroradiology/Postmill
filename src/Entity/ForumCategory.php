<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Selectable;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ForumCategoryRepository")
 * @ORM\Table(name="forum_categories", uniqueConstraints={
 *     @ORM\UniqueConstraint(name="forum_categories_name_idx", columns={"name"}),
 *     @ORM\UniqueConstraint(name="forum_categories_normalized_name_idx", columns={"normalized_name"})
 * })
 * @ApiResource()
 */
class ForumCategory {
    /**
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue()
     * @ORM\Id()
     *
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="text", unique=true)
     *
     * @var string
     */
    private $name;

    /**
     * @ORM\Column(type="text", unique=true)
     *
     * @var string
     */
    private $normalizedName;

    /**
     * @ORM\Column(type="text")
     *
     * @var string
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="text")
     */
    private $sidebar;

    /**
     * @ORM\OneToMany(targetEntity="Forum", mappedBy="category")
     * @ORM\OrderBy({"normalizedName": "ASC"})
     *
     * @var Forum[]|Collection|Selectable
     */
    private $forums;

    public function __construct(string $name, string $title, string $description, string $sidebar) {
        $this->setName($name);
        $this->title = $title;
        $this->description = $description;
        $this->sidebar = $sidebar;
        $this->forums = new ArrayCollection();
    }

    public function getId(): ?int {
        // todo: replace with UUID
        return $this->id;
    }

    public function getName(): string {
        return $this->name;
    }

    public function setName(string $name): void {
        $this->name = $name;
        $this->normalizedName = self::normalizeName($name);
    }

    public function getNormalizedName(): string {
        return $this->normalizedName;
    }

    public function getTitle(): string {
        return $this->title;
    }

    public function setTitle(string $title): void {
        $this->title = $title;
    }

    public function getDescription(): string {
        return $this->description;
    }

    public function setDescription(string $description): void {
        $this->description = $description;
    }

    public function getSidebar(): string {
        return $this->sidebar;
    }

    public function setSidebar(string $sidebar): void {
        $this->sidebar = $sidebar;
    }

    /**
     * @return Collection|Selectable|Forum[]
     */
    public function getForums(): Collection {
        return $this->forums;
    }

    public static function normalizeName(string $name) {
        return Forum::normalizeName($name);
    }
}
