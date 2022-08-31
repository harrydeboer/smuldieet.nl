<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\RecipeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[
    ORM\Entity(repositoryClass: RecipeRepository::class),
    ORM\Table(name: "recipe"),
    UniqueEntity(fields: ["title"], message: "Er is al een recept met deze titel."),
]
class Recipe
{
    use ImageTrait;
    use FoodWeightsTrait;

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
        'Antiliaans',
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
        'Filippijns',
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

    public const DIET_CHOICES = [
        'Vegetarisch' => 'vegetarian',
        'Veganistisch' => 'vegan',
        'Histamine vrij' => 'histamineFree',
        'Koemelk vrij' => 'cowMilkFree',
        'Soja vrij' => 'soyFree',
        'Gluten vrij' => 'glutenFree',
        'Kippenei eiwitvrij' => 'chickenEggProteinFree',
        'Noten vrij' => 'nutFree',
        'Zonder pakjes en zakjes' => 'withoutPackagesAndBags',
    ];

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

    #[ORM\Column(type: "bigint")]
    private int $timestamp;

    #[
        ORM\Column(type: "string", unique: true),
        Assert\NotBlank(message: 'De titel mag niet leeg zijn.'),
        Assert\Length(max: 255, maxMessage: 'De titel mag niet meer dan 255 tekens hebben.'),
        Assert\Regex(pattern: "/^[A-Za-zÀ-ÿ0-9\s_\-,.%\/\(\)\+<>'\"]+$/",
            message: "Toegestane tekens zijn letters, cijfers, spaties en _-,.%/()+<>'\"."),
    ]
    private string $title;

    #[
        ORM\Column(type: "string", unique: true, nullable: true),
    ]
    private ?string $url = null;

    #[
        ORM\Column(type: "text"),
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
        ORM\Column(type: "text", nullable: true),
        Assert\Length(max: 65535, maxMessage: 'De leuke verhaal mag niet meer dan 65535 tekens hebben.'),
    ]
    private ?string $niceStory = null;

    #[
        ORM\Column(type: "text", nullable: true),
        Assert\Length(max: 65535, maxMessage: 'De leuke tips mogen niet meer dan 65535 tekens hebben.'),
    ]
    private ?string $niceTips = null;

    #[
        ORM\Column(type: "text", nullable: true),
        Assert\Length(max: 65535, maxMessage: 'De hulpmiddelen/keukengerei mogen niet meer dan 65535 tekens hebben.'),
    ]
    private ?string $toolsAndKitchenware = null;

    #[
        ORM\Column(type: "integer"),
        Assert\GreaterThanOrEqual(1, message: 'Het aantal personen moet groter of gelijk zijn aan 1.'),
        Assert\LessThanOrEqual(2147483647, message: 'Het aantal personen moet kleiner of gelijk zijn aan 2147483647.'),
    ]
    private int $numberOfPersons;

    #[
        ORM\Column(type: "integer", nullable: true),
        Assert\GreaterThanOrEqual(1, message: 'Het aantal stuks moet groter of gelijk zijn aan 1.'),
        Assert\LessThanOrEqual(2147483647, message: 'Het aantal stuks moet kleiner of gelijk zijn aan 2147483647.'),
    ]
    private ?int $numberOfPieces = null;

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
    private bool $vegetarian = false;

    #[ORM\Column(type: "boolean")]
    private bool $vegan = false;

    #[ORM\Column(type: "boolean")]
    private bool $histamineFree = false;

    #[ORM\Column(type: "boolean")]
    private bool $cowMilkFree = false;

    #[ORM\Column(type: "boolean")]
    private bool $soyFree = false;

    #[ORM\Column(type: "boolean")]
    private bool $glutenFree = false;

    #[ORM\Column(type: "boolean")]
    private bool $chickenEggProteinFree = false;

    #[ORM\Column(type: "boolean")]
    private bool $nutFree = false;

    #[ORM\Column(type: "boolean")]
    private bool $withoutPackagesAndBags = false;

    #[
        ORM\ManyToOne(targetEntity: "App\Entity\User", inversedBy: "recipes"),
        ORM\JoinColumn(name: "user_id", referencedColumnName: "id", nullable: false),
    ]
    private User $user;

    #[
        ORM\ManyToMany(targetEntity: "Foodstuff", inversedBy: "recipes", indexBy: "id"),
        ORM\JoinTable(name: "recipe_foodstuff"),
        ORM\JoinColumn(name: "recipe_id", referencedColumnName: "id", onDelete: "CASCADE"),
        ORM\InverseJoinColumn(name: "foodstuff_id", referencedColumnName: "id", onDelete: "CASCADE"),
    ]
    private Collection $foodstuffs;

    #[ORM\ManyToMany(targetEntity: "Day", mappedBy: "recipes")]
    private Collection $days;

    #[ORM\OneToMany(mappedBy: "recipe", targetEntity: "Rating", cascade: ["remove"])]
    private Collection $ratings;

    #[ORM\OneToMany(mappedBy: "recipe", targetEntity: "Comment", cascade: ["remove"])]
    private Collection $comments;

    #[ORM\ManyToMany(targetEntity: "Cookbook", mappedBy: "recipes")]
    private Collection $cookbooks;

    #[ORM\ManyToMany(targetEntity: "Tag", mappedBy: "recipes")]
    private Collection $tags;

    #[ORM\Column(type: "boolean")]
    private bool $pending = true;

    public function __construct()
    {
        $this->foodstuffs = new ArrayCollection();
        $this->days = new ArrayCollection();
        $this->ratings = new ArrayCollection();
        $this->cookbooks = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->tags = new ArrayCollection();
    }

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

    public function getVegetarian(): bool
    {
        return $this->vegetarian;
    }

    public function setVegetarian(bool $vegetarian): void
    {
        $this->vegetarian = $vegetarian;
    }

    public function getVegan(): bool
    {
        return $this->vegan;
    }

    public function setVegan(bool $vegan): void
    {
        $this->vegan = $vegan;
    }

    public function getHistamineFree(): bool
    {
        return $this->histamineFree;
    }

    public function setHistamineFree(bool $histamineFree): void
    {
        $this->histamineFree = $histamineFree;
    }

    public function getCowMilkFree(): bool
    {
        return $this->cowMilkFree;
    }

    public function setCowMilkFree(bool $cowMilkFree): void
    {
        $this->cowMilkFree = $cowMilkFree;
    }

    public function getSoyFree(): bool
    {
        return $this->soyFree;
    }

    public function setSoyFree(bool $soyFree): void
    {
        $this->soyFree = $soyFree;
    }

    public function getGlutenFree(): bool
    {
        return $this->glutenFree;
    }

    public function setGlutenFree(bool $glutenFree): void
    {
        $this->glutenFree = $glutenFree;
    }

    public function getChickenEggProteinFree(): bool
    {
        return $this->chickenEggProteinFree;
    }

    public function setChickenEggProteinFree(bool $chickenEggProteinFree): void
    {
        $this->chickenEggProteinFree = $chickenEggProteinFree;
    }

    public function getNutFree(): bool
    {
        return $this->nutFree;
    }

    public function setNutFree(bool $nutFree): void
    {
        $this->nutFree = $nutFree;
    }

    public function getWithoutPackagesAndBags(): bool
    {
        return $this->withoutPackagesAndBags;
    }

    public function setWithoutPackagesAndBags(bool $withoutPackagesAndBags): void
    {
        $this->withoutPackagesAndBags = $withoutPackagesAndBags;
    }

    public function getFoodstuffs(): Collection
    {
        return $this->foodstuffs;
    }

    public function setFoodstuffs(Collection $foodstuffs): void
    {
        $this->foodstuffs = $foodstuffs;
    }

    public function addFoodstuff(Foodstuff $foodstuff): void
    {
        if ($this->foodstuffs->contains($foodstuff)) {
            return;
        }

        $this->foodstuffs->set($foodstuff->getId(), $foodstuff);
        $foodstuff->addRecipe($this);
    }

    public function removeFoodstuff(Foodstuff $foodstuff): void
    {
        if (!$this->foodstuffs->contains($foodstuff)) {
            return;
        }

        $this->foodstuffs->removeElement($foodstuff);
        $foodstuff->removeRecipe($this);
    }

    public function getDays(): Collection
    {
        return $this->days;
    }

    public function setDays(Collection $days): void
    {
        $this->days = $days;
    }

    public function addDay(Day $day): void
    {
        if ($this->days->contains($day)) {
            return;
        }

        $this->days->add($day);
        $day->addRecipe($this);
    }

    public function removeDay(Day $day): void
    {
        if (!$this->days->contains($day)) {
            return;
        }

        $this->days->removeElement($day);
        $day->removeRecipe($this);
    }

    public function addCookbook(Cookbook $cookbook): void
    {
        if ($this->cookbooks->contains($cookbook)) {
            return;
        }

        $this->cookbooks->add($cookbook);
        $cookbook->addRecipe($this);
    }

    public function removeCookbook(Cookbook $cookbook): void
    {
        if (!$this->cookbooks->contains($cookbook)) {
            return;
        }

        $this->cookbooks->removeElement($cookbook);
        $cookbook->removeRecipe($this);
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

    public function getCookbooks(): Collection
    {
        return $this->cookbooks;
    }

    public function setCookbooks(Collection $cookbooks): void
    {
        $this->cookbooks = $cookbooks;
    }

    public function getPending(): bool
    {
        return $this->pending;
    }

    public function setPending(bool $pending): void
    {
        $this->pending = $pending;
    }

    public function getIsSelfInvented(): bool
    {
        return $this->isSelfInvented;
    }

    public function setIsSelfInvented(bool $isSelfInvented): void
    {
        $this->isSelfInvented = $isSelfInvented;
    }

    public function getNiceStory(): ?string
    {
        return $this->niceStory;
    }

    public function setNiceStory(?string $niceStory): void
    {
        if (is_null($niceStory)) {
            $this->niceStory = null;
        } else {
            $this->niceStory = strip_tags($niceStory);
        }
    }

    public function getNumberOfPieces(): ?int
    {
        return $this->numberOfPieces;
    }

    public function setNumberOfPieces(?int $numberOfPieces): void
    {
        $this->numberOfPieces = $numberOfPieces;
    }

    public function getToolsAndKitchenware(): ?string
    {
        return $this->toolsAndKitchenware;
    }

    public function setToolsAndKitchenware(?string $toolsAndKitchenware): void
    {
        if (is_null($toolsAndKitchenware)) {
            $this->toolsAndKitchenware = null;
        } else {
            $this->toolsAndKitchenware = strip_tags($toolsAndKitchenware);
        }
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

    public function getNiceTips(): ?string
    {
        return $this->niceTips;
    }

    public function setNiceTips(?string $niceTips): void
    {
        if (is_null($niceTips)) {
            $this->niceTips = null;
        } else {
            $this->niceTips = strip_tags($niceTips);
        }
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
}
