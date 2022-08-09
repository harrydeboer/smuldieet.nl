<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CommentRepository;
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
    private int $timestamp;

    #[
        ORM\Column(type: "text"),
        Assert\Length(min: 1, max: 65535, minMessage: 'Het commentaar mag niet leeg zijn.',
            maxMessage: 'Het commentaar mag niet meer dan 65535 tekens hebben.'),
    ]
    private string $content;

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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    public function setTimestamp(int $timestamp): void
    {
        $this->timestamp = $timestamp;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = strip_tags($content);
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
