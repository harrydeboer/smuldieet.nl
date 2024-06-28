<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CommentRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ORM\Entity(repositoryClass: CommentRepository::class),
    ORM\Table(name: "comment"),
]
class Comment
{
    #[
        ORM\Id,
        ORM\Column(type: "integer"),
        ORM\GeneratedValue(strategy: "IDENTITY"),
    ]
    private int $id;

    #[
        ORM\Column(type: "bigint"),
    ]
    private int $createdAt;

    #[
        ORM\Column(type: "bigint", nullable: true),
    ]
    private ?int $updatedAt = null;

    #[
        ORM\Column(type: "text"),
        Assert\NotBlank(message: 'De content mag niet leeg zijn.'),
        Assert\Length(max: 65535, maxMessage: 'Het commentaar mag niet meer dan 65535 tekens hebben.'),
    ]
    private string $content;

    #[ORM\Column(type: "boolean")]
    private bool $pending = true;

    #[
        ORM\ManyToOne(targetEntity: "App\Entity\User", inversedBy: "comments"),
        ORM\JoinColumn(name: "user_id", referencedColumnName: "id", nullable: false),
    ]
    private User $user;

    #[
        ORM\ManyToOne(targetEntity: "App\Entity\Recipe", inversedBy: "comments"),
        ORM\JoinColumn(name: "recipe_id", referencedColumnName: "id", nullable: true),
    ]
    private ?Recipe $recipe = null;

    #[
        ORM\ManyToOne(targetEntity: "App\Entity\Page", inversedBy: "comments"),
        ORM\JoinColumn(name: "page_id", referencedColumnName: "id", nullable: true),
    ]
    private ?Page $page = null;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getCreatedAt(): int
    {
        return $this->createdAt;
    }

    public function setCreatedAt(int $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): ?int
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?int $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function getDate(): DateTime
    {
        $date = new DateTime();
        $date->setTimestamp($this->createdAt);
        return $date;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = strip_tags($content);
    }

    public function isPending(): bool
    {
        return $this->pending;
    }

    public function setPending(bool $pending): void
    {
        $this->pending = $pending;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function getRecipe(): ?Recipe
    {
        return $this->recipe;
    }

    public function setRecipe(?Recipe $recipe): void
    {
        $this->recipe = $recipe;
    }

    public function getPage(): ?Page
    {
        return $this->page;
    }

    public function setPage(?Page $page): void
    {
        $this->page = $page;
    }
}
