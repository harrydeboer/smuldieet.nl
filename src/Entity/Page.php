<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\PageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ORM\Entity(repositoryClass: PageRepository::class),
    ORM\Table(name: "page"),
    ORM\UniqueConstraint(name: "title_unique", fields: ["title"]),
    ORM\UniqueConstraint(name: "slug_unique", fields: ["slug"]),
    UniqueEntity(fields: ["title"], message: "Er is al een pagina met deze titel."),
    UniqueEntity(fields: ["slug"], message: "Er is al een pagina met deze slug."),
]
class Page
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
        ORM\Column(type: "string"),
        Assert\NotBlank(message: 'De titel mag niet leeg zijn.'),
        Assert\Length(max: 255, maxMessage: 'De titel mag niet meer dan 255 tekens hebben.'),
    ]
    private string $title;

    #[
        ORM\Column(type: "string"),
        Assert\NotBlank(message: 'De slug mag niet leeg zijn.'),
        Assert\Length(max: 255, maxMessage: 'De slug mag niet meer dan 255 tekens hebben.'),
    ]
    private string $slug;

    #[
        ORM\Column(type: "string", nullable: true),
        Assert\Length(max: 255, maxMessage: 'De samenvatting mag niet langer zijn dan 255 tekens.'),
    ]
    private ?string $summary = null;

    #[
        ORM\Column(type: "text"),
        Assert\NotBlank(message: 'De content mag niet leeg zijn.'),
        Assert\Length(max: 65535, maxMessage: 'De content mag niet meer dan 65535 tekens hebben.'),
    ]
    private string $content;

    #[
        ORM\ManyToOne(targetEntity: "App\Entity\User", inversedBy: "pages"),
        ORM\JoinColumn(name: "user_id", referencedColumnName: "id", nullable: false),
    ]
    private User $user;

    #[ORM\OneToMany(targetEntity: "App\Entity\Comment", mappedBy: "page", cascade: ["remove"])]
    private Collection $comments;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

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

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = strip_tags($title);
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): void
    {
        $this->slug = strtolower(strip_tags($slug));
    }

    public function getSummary(): ?string
    {
        return $this->summary;
    }

    public function setSummary(?string $summary): void
    {
        $this->summary = $summary;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function setComments(Collection $comments): void
    {
        $this->comments = $comments;
    }
}
