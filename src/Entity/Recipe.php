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
class Recipe implements FoodstuffWeightsInterface, UploadImageInterface
{
    public const COOKING_TIMES = ['0-10 min.', '10-20 min.', '20-30 min.', '30-60 min.', '> 1 uur', '> 2 uur'];

    public const OCCASION = [
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

    public const KITCHEN = [
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

    public const TYPE_OF_DISH = [
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
    protected int $id;

    #[ORM\Column(type: "bigint")]
    private int $timestamp;

    #[
        ORM\Column(type: "string"),
        Assert\NotBlank(message: 'De titel mag niet leeg zijn.'),
        Assert\Length(max: 255, maxMessage: 'De titel mag niet meer dan 255 tekens hebben.'),
        Assert\Regex(pattern: "/^[A-Za-z??-??0-9\s_\-,.%&\/\(\)\+<>'\"]+$/",
            message: "Toegestane tekens zijn letters, cijfers, spaties en _-,.%&/()+<>'\"."),
    ]
    private string $title;

    #[
        ORM\Column(type: "string", nullable: true),
    ]
    private ?string $url = null;

    #[
        ORM\Column(type: "text"),
        Assert\Length(max: 65535, maxMessage: 'De ingredi??nten mogen niet meer dan 65535 tekens hebben.'),
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
        Assert\Choice([], self::COOKING_TIMES, message: 'De bereidingstijd is niet een geldige optie.'),
    ]
    private string $cookingTime;

    #[
        ORM\Column(type: "string"),
        Assert\Choice([], self::KITCHEN, message: 'De keuken is niet een geldige optie.'),
    ]
    private string $kitchen;

    #[
        ORM\Column(type: "string", nullable: true),
        Assert\Choice([], self::OCCASION, message: 'De gelegenheid is niet een geldige optie.'),
    ]
    private ?string $occasion = null;

    #[
        ORM\Column(type: "string"),
        Assert\Choice([], self::TYPE_OF_DISH, message: 'Het type gerecht is niet een geldige optie.'),
    ]
    private string $typeOfDish;

    #[ORM\Column(type: "boolean")]
    private bool $isSelfInvented;

    #[ORM\Column(type: "string", nullable: true)]
    private ?string $source = null;

    #[ORM\Column(type: "boolean")]
    private bool $isVegetarian = false;

    #[ORM\Column(type: "boolean")]
    private bool $isVegan = false;

    #[ORM\Column(type: "boolean")]
    private bool $isHistamineFree = false;

    #[ORM\Column(type: "boolean")]
    private bool $isCowMilkFree = false;

    #[ORM\Column(type: "boolean")]
    private bool $isSoyFree = false;

    #[ORM\Column(type: "boolean")]
    private bool $isGlutenFree = false;

    #[ORM\Column(type: "boolean")]
    private bool $isChickenEggProteinFree = false;

    #[ORM\Column(type: "boolean")]
    private bool $isNutFree = false;

    #[ORM\Column(type: "boolean")]
    private bool $isWithoutPackagesAndBags = false;

    #[
        ORM\ManyToOne(targetEntity: "App\Entity\User", inversedBy: "recipes"),
        ORM\JoinColumn(name: "user_id", referencedColumnName: "id", nullable: false),
    ]
    private User $user;

    #[ORM\OneToMany(mappedBy: "recipe", targetEntity: "App\Entity\FoodstuffWeight", cascade: ["persist", "remove"])]
    private Collection $foodstuffWeights;

    #[ORM\OneToMany(mappedBy: "recipe", targetEntity: "App\Entity\RecipeWeight", cascade: ["persist", "remove"])]
    private Collection $recipeWeights;

    #[ORM\OneToMany(mappedBy: "recipe", targetEntity: "Rating", cascade: ["remove"])]
    private Collection $ratings;

    #[ORM\OneToMany(mappedBy: "recipe", targetEntity: "Comment", cascade: ["remove"])]
    private Collection $comments;

    #[ORM\ManyToMany(targetEntity: "Tag", mappedBy: "recipes")]
    private Collection $tags;

    #[ORM\ManyToMany(targetEntity: "User", mappedBy: "savedRecipes")]
    private Collection $users;

    #[ORM\Column(type: "boolean")]
    private bool $isPending = true;

    #[ORM\Column(type: "string", nullable: true)]
    private ?string $imageExtension = null;

    private ?UploadedFile $image = null;

    private array $tagsArray = [];

    public function __construct()
    {
        $this->foodstuffWeights = new ArrayCollection();
        $this->recipeWeights = new ArrayCollection();
        $this->ratings = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    public static function getDietChoices(string $camelOrSnake = 'camel'): array
    {
        $arrayCamel = [
            'isVegetarian' => 'Vegetarisch',
            'isVegan' => 'Veganistisch',
            'isHistamineFree' => 'Histamine vrij',
            'isCowMilkFree' => 'Koemelk vrij',
            'isSoyFree' => 'Soja vrij',
            'isGlutenFree' => 'Gluten vrij',
            'isChickenEggProteinFree' => 'Kippenei eiwitvrij',
            'isNutFree' => 'Noten vrij',
            'isWithoutPackagesAndBags' => 'Zonder pakjes en zakjes',
        ];

        if ($camelOrSnake === 'snake') {
            $arraySnake = [];
            foreach ($arrayCamel as $key => $item) {
                $arraySnake[strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $key))] = $item;
            }

            return $arraySnake;
        } else {

            return $arrayCamel;
        }
    }

    public function getId(): int
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

    public function getIsVegetarian(): bool
    {
        return $this->isVegetarian;
    }

    public function setIsVegetarian(bool $isVegetarian): void
    {
        $this->isVegetarian = $isVegetarian;
    }

    public function getIsVegan(): bool
    {
        return $this->isVegan;
    }

    public function setIsVegan(bool $isVegan): void
    {
        $this->isVegan = $isVegan;
    }

    public function getIsHistamineFree(): bool
    {
        return $this->isHistamineFree;
    }

    public function setIsHistamineFree(bool $isHistamineFree): void
    {
        $this->isHistamineFree = $isHistamineFree;
    }

    public function getIsCowMilkFree(): bool
    {
        return $this->isCowMilkFree;
    }

    public function setIsCowMilkFree(bool $isCowMilkFree): void
    {
        $this->isCowMilkFree = $isCowMilkFree;
    }

    public function getIsSoyFree(): bool
    {
        return $this->isSoyFree;
    }

    public function setIsSoyFree(bool $isSoyFree): void
    {
        $this->isSoyFree = $isSoyFree;
    }

    public function getIsGlutenFree(): bool
    {
        return $this->isGlutenFree;
    }

    public function setIsGlutenFree(bool $isGlutenFree): void
    {
        $this->isGlutenFree = $isGlutenFree;
    }

    public function getIsChickenEggProteinFree(): bool
    {
        return $this->isChickenEggProteinFree;
    }

    public function setIsChickenEggProteinFree(bool $isChickenEggProteinFree): void
    {
        $this->isChickenEggProteinFree = $isChickenEggProteinFree;
    }

    public function getIsNutFree(): bool
    {
        return $this->isNutFree;
    }

    public function setIsNutFree(bool $isNutFree): void
    {
        $this->isNutFree = $isNutFree;
    }

    public function getIsWithoutPackagesAndBags(): bool
    {
        return $this->isWithoutPackagesAndBags;
    }

    public function setIsWithoutPackagesAndBags(bool $isWithoutPackagesAndBags): void
    {
        $this->isWithoutPackagesAndBags = $isWithoutPackagesAndBags;
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

    public function getIsPending(): bool
    {
        return $this->isPending;
    }

    public function setIsPending(bool $isPending): void
    {
        $this->isPending = $isPending;
    }

    public function getIsSelfInvented(): bool
    {
        return $this->isSelfInvented;
    }

    public function setIsSelfInvented(bool $isSelfInvented): void
    {
        $this->isSelfInvented = $isSelfInvented;
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

    public function getTagsArray(): array
    {
        $tagsArray = $this->tagsArray;
        foreach ($this->tags as $tag) {
            $tagsArray[] = $tag->getName();
        }

        return $tagsArray;
    }

    public function setTags(Collection $tags): void
    {
        $this->tags = $tags;
    }

    public function setTagsArray(array $tagsArray): void
    {
        $this->tagsArray = $tagsArray;
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
        $this->foodstuffWeights = $foodstuffWeights;
    }

    public function addFoodstuffWeight(FoodstuffWeight $foodstuffWeight): void
    {
        $foodstuffWeight->setRecipe($this);
        $this->foodstuffWeights->add($foodstuffWeight);
    }

    public function removeFoodstuffWeight(FoodstuffWeight $foodstuffWeight): void
    {
        $this->foodstuffWeights->removeElement($foodstuffWeight);
    }

    public function getRecipeWeights(): Collection
    {
        return $this->recipeWeights;
    }

    public function setRecipeWeights(Collection $recipeWeights): void
    {
        $this->recipeWeights = $recipeWeights;
    }

    public function getImageExtension(): ?string
    {
        return $this->imageExtension;
    }

    public function setImageExtension(?string $imageExtension): void
    {
        $this->imageExtension = $imageExtension;
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
    public function getImageUrl(int $width = null, string $extraPath = ''): ?string
    {
        return 'uploads/recipe/images/' . $extraPath . $this->getId() . '_' . $width . '.' . $this->getImageExtension();
    }
}
