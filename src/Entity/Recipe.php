<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\RecipeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ORM\Entity(repositoryClass: RecipeRepository::class),
    ORM\Table(name: "recipe"),
]
class Recipe implements DietInterface, UploadImageInterface
{
    public const array COOKING_TIMES = ['0-10 min.', '10-20 min.', '20-30 min.', '30-60 min.', '> 1 uur', '> 2 uur'];

    public const array OCCASION = [
        'Familiediner',
        'Halloween',
        'Kerstmis',
        'Kinderfeestje',
        'Moederdag',
        'Nieuwjaar',
        'Oudjaar',
        'Pasen',
        'Receptie',
        'Sinterklaas',
        'Suikerfeest',
        'Vakantie',
        'Valentijn',
        'Verjaardag',
    ];

    public const array KITCHEN = [
        'Afrikaans',
        'Amerikaans',
        'Antilliaans',
        'Arabisch',
        'Argentijns',
        'Australisch',
        'Aziatisch',
        'Balkan',
        'Belgisch',
        'Biologisch',
        'Braziliaans',
        'Bulgaars',
        'Canadees',
        'Caribisch',
        'Chileens',
        'Chinees',
        'Duits',
        'Engels',
        'Europees',
        'Filipijns',
        'Frans',
        'Fusion',
        'Ghanees',
        'Goedkoop en snel',
        'Grieks',
        'Hongaars',
        'Iers',
        'Indiaas',
        'Indisch',
        'Indonesisch',
        'Internationaal',
        'Iraans',
        'Italiaans',
        'Japans',
        'Joegoslavisch',
        'Joods',
        'Kinderkeuken',
        'Koreaans',
        'Marokkaans',
        'Mediterraan',
        'Mexicaans',
        'Montignac',
        'Multi-cultureel',
        'Nederlands',
        'Oostenrijks',
        'Pools',
        'Portugees',
        'Regionaal',
        'Russisch',
        'Scandinavisch',
        'Schots',
        'Slanke keuken',
        'Slowaaks',
        'Spaans',
        'Surinaams',
        'Thais',
        'Tsjechisch',
        'Turks',
        'Vegetarisch',
        'Vietnamees',
        'Vis',
        'Wereld',
        'Zwitsers',
    ];

    public const array TYPE_OF_DISH = [
        'Alcoholische dranken',
        'Amuse',
        'Banket',
        'Bijgerecht',
        'Borrelhapje',
        'Brood',
        'Buffet',
        'Feestmaaltijd',
        'Hoofdgerecht',
        'Lunch/Brunch',
        'Nagerecht',
        'Niet-alcoholische dranken',
        'Ontbijt',
        'Ovenschotel',
        'Salade',
        'Saus/dressing',
        'Soep',
        'Streekgerecht',
        'Tussendoortje',
        'Tussengerecht',
        'Vegetarisch',
        'Voorgerecht',
    ];

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
        Assert\Regex(pattern: "/^[A-Za-zÀ-ÿ0-9\s_\-,.%&\/\(\)\+<>'\"]+$/",
            message: "Toegestane tekens zijn letters, cijfers, spaties en _-,.%&/()+<>'\"."),
    ]
    private string $title;

    #[
        ORM\Column(type: "string", nullable: true),
    ]
    private ?string $url = null;

    #[
        ORM\Column(type: "text"),
        Assert\NotBlank(message: 'De ingrediënten mogen niet leeg zijn.'),
        Assert\Length(max: 65535, maxMessage: 'De ingrediënten mogen niet meer dan 65535 tekens hebben.'),
    ]
    private string $ingredients;

    #[
        ORM\Column(type: "text"),
        Assert\NotBlank(message: 'De bereidingswijze mag niet leeg zijn.'),
        Assert\Length(max: 65535, maxMessage: 'De bereidingswijze mag niet meer dan 65535 tekens hebben.'),
    ]
    private string $preparationMethod;

    #[
        ORM\Column(type: "integer"),
        Assert\NotBlank(message: 'Het aantal personen mag niet leeg zijn.'),
        Assert\GreaterThanOrEqual(1, message: 'Het aantal personen moet groter of gelijk zijn aan 1.'),
        Assert\LessThanOrEqual(2147483647, message: 'Het aantal personen moet kleiner of gelijk zijn aan 2147483647.'),
    ]
    private int $numberOfPersons;

    #[
        ORM\Column(type: "float", nullable: true),
        Assert\GreaterThanOrEqual(1, message: 'De rating moet groter of gelijk zijn aan 1.'),
        Assert\LessThanOrEqual(10, message: 'De rating moet kleiner of gelijk zijn aan 10.'),
    ]
    private ?float $rating = null;

    #[
        ORM\Column(type: "integer"),
        Assert\GreaterThanOrEqual(0, message: 'Het aantal keer bewaard moet groter of gelijk zijn aan 0.'),
        Assert\LessThanOrEqual(2147483647, message: 'Het aantal stemmen moet kleiner of gelijk zijn aan 2147483647.'),
    ]
    private int $votes = 0;

    #[
        ORM\Column(type: "integer"),
        Assert\GreaterThanOrEqual(0, message: 'Het aantal keer bewaard moet groter of gelijk zijn aan 0.'),
        Assert\LessThanOrEqual(2147483647,
            message: 'Het aantal keer bewaard moet kleiner of gelijk zijn aan 2147483647.'),
    ]
    private int $timesSaved = 0;

    #[
        ORM\Column(type: "integer"),
        Assert\GreaterThanOrEqual(0, message: 'Het aantal reacties moet groter of gelijk zijn aan 0.'),
        Assert\LessThanOrEqual(2147483647,
            message: 'Het aantal reacties moet kleiner of gelijk zijn aan 2147483647.'),
    ]
    private int $timesReacted = 0;

    #[
        ORM\Column(type: "string"),
        Assert\NotBlank(message: 'De bereidingstijd mag niet leeg zijn.'),
        Assert\Choice(null, self::COOKING_TIMES, message: 'De bereidingstijd is niet een geldige optie.'),
    ]
    private string $cookingTime;

    #[
        ORM\Column(type: "string"),
        Assert\NotBlank(message: 'De keuken mag niet leeg zijn.'),
        Assert\Choice(null, self::KITCHEN, message: 'De keuken is niet een geldige optie.'),
    ]
    private string $kitchen;

    #[
        ORM\Column(type: "string", nullable: true),
        Assert\Choice(null, self::OCCASION, message: 'De gelegenheid is niet een geldige optie.'),
    ]
    private ?string $occasion = null;

    #[
        ORM\Column(type: "string"),
        Assert\NotBlank(message: 'Het type gerecht mag niet leeg zijn.'),
        Assert\Choice(null, self::TYPE_OF_DISH, message: 'Het type gerecht is niet een geldige optie.'),
    ]
    private string $typeOfDish;

    #[
        ORM\Column(type: "boolean"),
    ]
    private bool $selfInvented = false;

    #[ORM\Column(type: "string", nullable: true)]
    private ?string $source = null;

    #[
        ORM\ManyToOne(targetEntity: "App\Entity\User", inversedBy: "recipes"),
        ORM\JoinColumn(name: "user_id", referencedColumnName: "id", nullable: false),
    ]
    private User $user;

    #[
        ORM\OneToMany(targetEntity: "App\Entity\RecipeFoodstuffWeight", mappedBy: "recipe",
            cascade: ["persist", "remove"]),
        Assert\Valid,
    ]
    private Collection $foodstuffWeights;

    #[ORM\OneToMany(targetEntity: "App\Entity\CookbookRecipeWeight", mappedBy: "recipe",
        cascade: ["persist", "remove"])]
    private Collection $cookbookRecipeWeights;

    #[ORM\OneToMany(targetEntity: "App\Entity\DayRecipeWeight", mappedBy: "recipe", cascade: ["persist", "remove"])]
    private Collection $dayRecipeWeights;

    #[ORM\OneToMany(targetEntity: "App\Entity\Rating", mappedBy: "recipe", cascade: ["remove"])]
    private Collection $ratings;

    #[ORM\OneToMany(targetEntity: "App\Entity\Comment", mappedBy: "recipe", cascade: ["remove"])]
    private Collection $comments;

    #[ORM\ManyToMany(targetEntity: "App\Entity\Tag", mappedBy: "recipes")]
    private Collection $tags;

    #[ORM\ManyToMany(targetEntity: "App\Entity\User", mappedBy: "savedRecipes")]
    private Collection $users;

    #[ORM\Column(type: "boolean")]
    private bool $pending = true;

    #[ORM\Column(type: "string", nullable: true)]
    private ?string $imageExtension = null;

    private ?UploadedFile $image = null;

    #[ORM\Column(type: "boolean")]
    protected bool $vegetarian = false;

    #[ORM\Column(type: "boolean")]
    protected bool $vegan = false;

    #[ORM\Column(type: "boolean")]
    protected bool $histamineFree = false;

    #[ORM\Column(type: "boolean")]
    protected bool $cowMilkFree = false;

    #[ORM\Column(type: "boolean")]
    protected bool $soyFree = false;

    #[ORM\Column(type: "boolean")]
    protected bool $glutenFree = false;

    #[ORM\Column(type: "boolean")]
    protected bool $chickenEggProteinFree = false;

    #[ORM\Column(type: "boolean")]
    protected bool $nutFree = false;

    #[ORM\Column(type: "boolean")]
    protected bool $withoutPackagesAndBags = false;

    public function __construct()
    {
        $this->foodstuffWeights = new ArrayCollection();
        $this->cookbookRecipeWeights = new ArrayCollection();
        $this->dayRecipeWeights = new ArrayCollection();
        $this->ratings = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->users = new ArrayCollection();
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

    public function getPreparationMethod(): string
    {
        return $this->preparationMethod;
    }

    public function setPreparationMethod(string $preparationMethod): void
    {
        $this->preparationMethod = strip_tags($preparationMethod);
    }

    public function getIngredients(): string
    {
        return $this->ingredients;
    }

    public function setIngredients(string $ingredients): void
    {
        $this->ingredients = strip_tags($ingredients);
    }

    public function getNumberOfPersons(): int
    {
        return $this->numberOfPersons;
    }

    public function setNumberOfPersons(int $numberOfPersons): void
    {
        $this->numberOfPersons = $numberOfPersons;
    }

    public function getRating(): ?float
    {
        return $this->rating;
    }

    public function setRating(?float $rating): void
    {
        $this->rating = $rating;
    }

    public function getVotes(): int
    {
        return $this->votes;
    }

    public function setVotes(int $votes): void
    {
        $this->votes = $votes;
    }

    public function getTimesSaved(): int
    {
        return $this->timesSaved;
    }

    public function setTimesSaved(int $timesSaved): void
    {
        $this->timesSaved = $timesSaved;
    }

    public function getTimesReacted(): int
    {
        return $this->timesReacted;
    }

    public function setTimesReacted(int $timesReacted): void
    {
        $this->timesReacted = $timesReacted;
    }

    public function getOccasion(): ?string
    {
        return $this->occasion;
    }

    public function setOccasion(?string $occasion): void
    {
        if (!in_array($occasion, self::OCCASION)) {
            throw new InvalidArgumentException("Invalid occasion.");
        }
        $this->occasion = $occasion;
    }

    public function getCookingTime(): string
    {
        return $this->cookingTime;
    }

    public function setCookingTime(string $cookingTime): void
    {
        if (!in_array($cookingTime, self::COOKING_TIMES)) {
            throw new InvalidArgumentException("Invalid cooking time.");
        }
        $this->cookingTime = $cookingTime;
    }

    public function getKitchen(): string
    {
        return $this->kitchen;
    }

    public function setKitchen(string $kitchen): void
    {
        if (!in_array($kitchen, self::KITCHEN)) {
            throw new InvalidArgumentException("Invalid kitchen.");
        }
        $this->kitchen = $kitchen;
    }

    public function getTypeOfDish(): string
    {
        return $this->typeOfDish;
    }

    public function setTypeOfDish(string $typeOfDish): void
    {
        if (!in_array($typeOfDish, self::TYPE_OF_DISH)) {
            throw new InvalidArgumentException("Invalid type of dish.");
        }
        $this->typeOfDish = $typeOfDish;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
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

    public function isPending(): bool
    {
        return $this->pending;
    }

    public function setPending(bool $pending): void
    {
        $this->pending = $pending;
    }

    public function isSelfInvented(): bool
    {
        return $this->selfInvented;
    }

    public function setSelfInvented(bool $selfInvented): void
    {
        $this->selfInvented = $selfInvented;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): void
    {
        if (is_null($url)) {
            $this->url = null;
        } else {
            $this->url = strip_tags($url);
        }
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setSource(?string $source): void
    {
        $this->source = $source;
    }

    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function setTags(Collection $tags): void
    {
        $this->tags = $tags;
    }

    public function addTag(Tag $tag): void
    {
        if ($this->tags->contains($tag)) {
            return;
        }

        $this->tags->add($tag);
        $tag->addRecipe($this);
    }

    public function removeTag(Tag $tag): void
    {
        if (!$this->tags->contains($tag)) {
            return;
        }

        $this->tags->removeElement($tag);
        $tag->removeRecipe($this);
    }

    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function setUsers(Collection $users): void
    {
        $this->users = $users;
    }

    public function addUser(User $user): void
    {
        if ($this->users->contains($user)) {
            return;
        }

        $this->users->add($user);
        $user->addSavedRecipe($this);
    }

    public function removeUser(User $user): void
    {
        if (!$this->users->contains($user)) {
            return;
        }

        $this->users->removeElement($user);
        $user->removeSavedRecipe($this);
    }

    public function getFoodstuffWeights(): Collection
    {
        return $this->foodstuffWeights;
    }

    public function setFoodstuffWeights(Collection $foodstuffWeights): void
    {
        foreach ($foodstuffWeights as $foodstuffWeight) {
            $foodstuffWeight->setRecipe($this);
        }
        $this->foodstuffWeights = $foodstuffWeights;
    }

    public function addFoodstuffWeight(RecipeFoodstuffWeight $foodstuffWeight): void
    {
        $foodstuffWeight->setRecipe($this);
        $this->foodstuffWeights->add($foodstuffWeight);
    }

    public function removeFoodstuffWeight(RecipeFoodstuffWeight $foodstuffWeight): void
    {
        $this->foodstuffWeights->removeElement($foodstuffWeight);
    }

    public function getCookbookRecipeWeights(): Collection
    {
        return $this->cookbookRecipeWeights;
    }

    public function setCookbookRecipeWeights(Collection $recipeWeights): void
    {
        foreach ($recipeWeights as $recipeWeight) {
            $recipeWeight->setRecipe($this);
        }
        $this->cookbookRecipeWeights = $recipeWeights;
    }

    public function getDayRecipeWeights(): Collection
    {
        return $this->dayRecipeWeights;
    }

    public function setDayRecipeWeights(Collection $recipeWeights): void
    {
        foreach ($recipeWeights as $recipeWeight) {
            $recipeWeight->setRecipe($this);
        }
        $this->dayRecipeWeights = $recipeWeights;
    }

    public function getImageExtension(): ?string
    {
        return $this->imageExtension;
    }

    public function setImageExtension(?string $imageExtension): void
    {
        $this->imageExtension = strtolower($imageExtension);
    }


    public function getImage(): ?UploadedFile
    {
        return $this->image;
    }

    public function setImage(?UploadedFile $image): void
    {
        if (!is_null($image)) {
            $this->setImageExtension($image->getClientOriginalExtension());
        }
        $this->image = $image;
    }

    public function getImageWidths(): array
    {
        return [
            100,
            600,
        ];
    }

    /**
     * Get the path of the image with respect to the public folder.
     */
    public function getImageUrl(?int $width = null, string $extraPath = ''): ?string
    {
        if (is_null($width)) {
            $hyphen = '';
        } else {
            $hyphen = '-';
        }
        return 'uploads/recipe/images/' . $extraPath . $this->getId() .
            $hyphen . $width . '.' . $this->getImageExtension();
    }

    public function isVegetarian(): bool
    {
        return $this->vegetarian;
    }

    public function setVegetarian(bool $vegetarian): void
    {
        $this->vegetarian = $vegetarian;
    }

    public function isVegan(): bool
    {
        return $this->vegan;
    }

    public function setVegan(bool $vegan): void
    {
        $this->vegan = $vegan;
    }

    public function isHistamineFree(): bool
    {
        return $this->histamineFree;
    }

    public function setHistamineFree(bool $histamineFree): void
    {
        $this->histamineFree = $histamineFree;
    }

    public function isCowMilkFree(): bool
    {
        return $this->cowMilkFree;
    }

    public function setCowMilkFree(bool $cowMilkFree): void
    {
        $this->cowMilkFree = $cowMilkFree;
    }

    public function isSoyFree(): bool
    {
        return $this->soyFree;
    }

    public function setSoyFree(bool $soyFree): void
    {
        $this->soyFree = $soyFree;
    }

    public function isGlutenFree(): bool
    {
        return $this->glutenFree;
    }

    public function setGlutenFree(bool $glutenFree): void
    {
        $this->glutenFree = $glutenFree;
    }

    public function isChickenEggProteinFree(): bool
    {
        return $this->chickenEggProteinFree;
    }

    public function setChickenEggProteinFree(bool $chickenEggProteinFree): void
    {
        $this->chickenEggProteinFree = $chickenEggProteinFree;
    }

    public function isNutFree(): bool
    {
        return $this->nutFree;
    }

    public function setNutFree(bool $nutFree): void
    {
        $this->nutFree = $nutFree;
    }

    public function isWithoutPackagesAndBags(): bool
    {
        return $this->withoutPackagesAndBags;
    }

    public function setWithoutPackagesAndBags(bool $withoutPackagesAndBags): void
    {
        $this->withoutPackagesAndBags = $withoutPackagesAndBags;
    }

    public static function getDietChoices(bool $isSnake = false): array
    {
        $dietChoicesCamel = DietInterface::CHOICES;

        if (!$isSnake) {
            return $dietChoicesCamel;
        }

        $dietChoicesSnake = [];
        foreach ($dietChoicesCamel as $key => $name) {
            $dietChoicesSnake[strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $key))] = $name;
        }

        return $dietChoicesSnake;
    }
}
