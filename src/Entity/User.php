<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ORM\Entity(repositoryClass: UserRepository::class),
    ORM\Table(name: "user"),
    ORM\UniqueConstraint(fields: ["username"]),
    ORM\UniqueConstraint(fields: ["email"]),
    UniqueEntity(fields: ["username"], message: "Er is al een gebruiker met deze gebruikersnaam."),
    UniqueEntity(fields: ["email"], message: "Er is al een gebruiker met dit e-mailadres."),
]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use UploadImageTrait;

    public const GENDER = ['man', 'vrouw'];

    public const IMAGE_WIDTHS = [
        100,
        600,
    ];

    #[
        ORM\Id,
        ORM\Column(type: "integer"),
        ORM\GeneratedValue(strategy: "IDENTITY"),
    ]
    protected int $id;

    #[
        ORM\Column(type: "string", length: 180, nullable: true),
        Assert\Length(max: 180, maxMessage: 'De voornaam mag niet meer dan 180 tekens hebben.'),
        Assert\Regex(pattern: "/^[A-Za-zÀ-ÿ\s\-]+$/",
            message: "Toegestane tekens zijn letters en vliegend streepje."),
    ]
    private ?string $firstName = null;

    #[
        ORM\Column(type: "string", length: 180, nullable: true),
        Assert\Length(max: 180, maxMessage: 'De achternaam mag niet meer dan 180 tekens hebben.'),
        Assert\Regex(pattern: "/^[A-Za-zÀ-ÿ\s\-]+$/",
            message: "Toegestane tekens zijn letters en vliegend streepje."),
    ]
    private ?string $lastName = null;

    #[
        ORM\Column(type: "string", length: 180),
        Assert\NotBlank(message: 'De gebruikersnaam mag niet leeg zijn.'),
        Assert\Length(max: 180, maxMessage: 'De gebruikersnaam mag niet meer dan 180 tekens hebben.'),
        Assert\Regex(pattern: "/^[A-Za-z0-9_\-]+$/", message: "Toegestane tekens zijn letters, cijfers en streepjes."),
    ]
    private string $username;

    #[
        ORM\Column(type: "string", length: 180),
        Assert\NotBlank(message: 'De e-mail mag niet leeg zijn.'),
        Assert\Length(max: 180, maxMessage: 'Het e-mailadres mag niet meer dan 180 tekens hebben.'),
        Assert\Email,
    ]
    private string $email;

    #[
        ORM\Column(type: "string"),
        Assert\Choice([], self::GENDER, message: 'Het geslacht is niet een geldige optie.'),
    ]
    private string $gender;

    #[
        ORM\Column(type: "float"),
        Assert\GreaterThan(0, message: 'Het gewicht moet groter zijn dan 0 kg.'),
        Assert\LessThan(1000, message: 'Het gewicht moet kleiner zijn dan 1000 kg.'),
    ]
    private float $weight;

    #[
        ORM\Column(type: "bigint"),
    ]
    private int $timestamp;

    #[ORM\Column(type: "json")]
    private array $roles = [];

    #[
        ORM\Column(type: "string"),
    ]
    private string $password;

    #[ORM\Column(type: "boolean")]
    private bool $isVerified = false;

    #[ORM\OneToMany(mappedBy: "user", targetEntity: "App\Entity\Day", cascade: ["remove"])]
    private Collection $days;

    #[ORM\OneToMany(mappedBy: "user", targetEntity: "App\Entity\Foodstuff", cascade: ["remove"])]
    private Collection $foodstuffs;

    #[ORM\OneToMany(mappedBy: "user", targetEntity: "App\Entity\Recipe", cascade: ["remove"])]
    private Collection $recipes;

    #[ORM\OneToMany(mappedBy: "user", targetEntity: "App\Entity\Cookbook", cascade: ["remove"])]
    private Collection $cookbooks;

    #[ORM\OneToMany(mappedBy: "user", targetEntity: "App\Entity\Rating", cascade: ["remove"])]
    private Collection $ratings;

    #[ORM\OneToMany(mappedBy: "user", targetEntity: "App\Entity\Page", cascade: ["remove"])]
    private Collection $pages;

    #[ORM\OneToMany(mappedBy: "user", targetEntity: "App\Entity\Comment", cascade: ["remove"])]
    private Collection $comments;

    public function __construct()
    {
        $this->days = new ArrayCollection();
        $this->foodstuffs = new ArrayCollection();
        $this->recipes = new ArrayCollection();
        $this->cookbooks = new ArrayCollection();
        $this->ratings = new ArrayCollection();
        $this->pages = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): void
    {
        if (is_null($firstName)) {
            $this->firstName = null;
        } else {
            $this->firstName = strip_tags($firstName);
        }
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): void
    {
        if (is_null($lastName)) {
            $this->lastName = null;
        } else {
            $this->lastName = strip_tags($lastName);
        }
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = strip_tags($username);
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getGender(): string
    {
        return $this->gender;
    }

    public function setGender(string $gender): void
    {
        if (!in_array($gender, self::GENDER)) {
            throw new InvalidArgumentException("Invalid gender.");
        }
        $this->gender = $gender;
    }

    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    public function setTimestamp(int $timestamp): void
    {
        $this->timestamp = $timestamp;
    }

    public function getBirthdate(): DateTime
    {
        $date = new DateTime();
        $date->setTimestamp($this->timestamp);
        return $date;
    }

    public function setBirthdate(DateTime $date): void
    {
        $this->timestamp = $date->getTimestamp();
    }

    public function getWeight(): float
    {
        return $this->weight;
    }

    public function setWeight(float $weight): void
    {
        $this->weight = $weight;
    }


    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        if ($this->isVerified()) {
            $roles[] = 'ROLE_VERIFIED';
        }

        return array_unique($roles);
    }

    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): void
    {
        $this->isVerified = $isVerified;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function eraseCredentials()
    {
    }

    public function getDays(): Collection
    {
        return $this->days;
    }

    public function setDays(Collection $days): void
    {
        $this->days = $days;
    }

    public function getFoodstuffs(): Collection
    {
        return $this->foodstuffs;
    }

    public function setFoodstuffs(Collection $foodstuffs): void
    {
        $this->foodstuffs = $foodstuffs;
    }

    public function getRecipes(): Collection
    {
        return $this->recipes;
    }

    public function setRecipes(Collection $recipes): void
    {
        $this->recipes = $recipes;
    }

    public function getCookbooks(): Collection
    {
        return $this->cookbooks;
    }

    public function setCookbooks(Collection $cookbooks): void
    {
        $this->cookbooks = $cookbooks;
    }

    public function getRatings(): Collection
    {
        return $this->ratings;
    }

    public function setRatings(Collection $ratings): void
    {
        $this->ratings = $ratings;
    }

    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function setComments(Collection $comments): void
    {
        $this->comments = $comments;
    }

    public function getPages(): Collection
    {
        return $this->pages;
    }

    public function setPages(Collection $pages): void
    {
        $this->pages = $pages;
    }
}
