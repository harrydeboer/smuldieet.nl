<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\FoodstuffRepository;

#[
    ORM\Entity(repositoryClass: FoodstuffRepository::class),
    ORM\Table(name: "foodstuff"),
    ORM\UniqueConstraint(name: "name_unique", columns: ["user_id", "name"]),
    UniqueEntity(fields: ["user", "name"], message: "Er is al een voedingsmiddel met deze naam."),
]
class Foodstuff implements NutrientsInterface
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
        ORM\ManyToOne(targetEntity: "App\Entity\User", inversedBy: "foodstuffs"),
        ORM\JoinColumn(name: "user_id", referencedColumnName: "id", nullable: true),
    ]
    private ?User $user = null;

    #[
        ORM\Column(type: "string"),
        Assert\NotBlank(message: 'De naam mag niet leeg zijn.'),
        Assert\Length(max: 255, maxMessage: 'De naam mag niet meer dan 255 tekens hebben.'),
        Assert\Regex(pattern: "/^[A-Za-zÀ-ÿ0-9\s_\-,.%&\/\(\)\+<>'\"]+$/",
            message: "Toegestane tekens zijn letters, cijfers, spaties en _-,.%&/()+<>'\"."),
    ]
    private string $name;

    #[
        ORM\Column(type: "string", nullable: true),
        Assert\Length(max: 255, maxMessage: 'De naam mag niet meer dan 255 tekens hebben.'),
        Assert\Regex(pattern: "/^[A-Za-zÀ-ÿ0-9\s_\-,.%\/\(\)\+<>'\"]+$/",
            message: "Toegestane tekens zijn letters, cijfers, spaties en _-,.%/()+<>'\"."),
    ]
    private ?string $pieceName = null;

    #[
        ORM\Column(type: "string", nullable: true),
        Assert\Length(max: 255, maxMessage: 'De naam mag niet meer dan 255 tekens hebben.'),
        Assert\Regex(pattern: "/^[A-Za-zÀ-ÿ0-9\s_\-,.%\/\(\)\+<>'\"]+$/",
            message: "Toegestane tekens zijn letters, cijfers, spaties en _-,.%/()+<>'\"."),
    ]
    private ?string $piecesName = null;

    #[
        ORM\Column(type: "float", nullable: true),
        Assert\GreaterThanOrEqual(0, message: 'Gewicht per stuk moet groter of gelijk aan 0 zijn.'),
    ]
    private ?float $pieceWeight = null;

    #[ORM\Column(type: "boolean")]
    private bool $liquid = false;

    #[
        ORM\Column(type: "float", nullable: true),
        Assert\GreaterThanOrEqual(0, message: 'Dichtheid moet groter of gelijk aan 0 zijn.'),
    ]
    private ?float $density = null;

    #[
        ORM\Column(type: "float", nullable: true),
        Assert\GreaterThanOrEqual(0, message: 'Energie moet groter of gelijk aan 0 zijn.'),
    ]
    protected ?float $energy = null;

    #[
        ORM\Column(type: "float", nullable: true),
        Assert\GreaterThanOrEqual(0, message: 'Water moet groter of gelijk aan 0 zijn.'),
    ]
    protected ?float $water = null;

    #[
        ORM\Column(type: "float", nullable: true),
        Assert\GreaterThanOrEqual(0, message: 'Eiwit moet groter of gelijk aan 0 zijn.'),
    ]
    protected ?float $protein = null;

    #[
        ORM\Column(type: "float", nullable: true),
        Assert\GreaterThanOrEqual(0, message: 'Koolhydraten moet groter of gelijk aan 0 zijn.'),
    ]
    protected ?float $carbohydrates = null;

    #[
        ORM\Column(type: "float", nullable: true),
        Assert\GreaterThanOrEqual(0, message: 'Suiker moet groter of gelijk aan 0 zijn.'),
    ]
    protected ?float $sucre = null;

    #[
        ORM\Column(type: "float", nullable: true),
        Assert\GreaterThanOrEqual(0, message: 'Vet moet groter of gelijk aan 0 zijn.'),
    ]
    protected ?float $fat = null;

    #[
        ORM\Column(type: "float", nullable: true),
        Assert\GreaterThanOrEqual(0, message: 'Verzadigd vet moet groter of gelijk aan 0 zijn.'),
    ]
    protected ?float $saturatedFat = null;

    #[
        ORM\Column(type: "float", nullable: true),
        Assert\GreaterThanOrEqual(0, message: 'Enkelvoudig verzadigd vet moet groter of gelijk aan 0 zijn.'),
    ]
    protected ?float $monounsaturatedFat = null;

    #[
        ORM\Column(type: "float", nullable: true),
        Assert\GreaterThanOrEqual(0, message: 'Meervoudig verzadigd vet moet groter of gelijk aan 0 zijn.'),
    ]
    protected ?float $polyunsaturatedFat = null;

    #[
        ORM\Column(type: "float", nullable: true),
        Assert\GreaterThanOrEqual(0, message: 'Cholesterol moet groter of gelijk aan 0 zijn.'),
    ]
    protected ?float $cholesterol = null;

    #[
        ORM\Column(type: "float", nullable: true),
        Assert\GreaterThanOrEqual(0, message: 'Voedingsvezel moet groter of gelijk aan 0 zijn.'),
    ]
    protected ?float $dietaryFiber = null;

    #[
        ORM\Column(type: "float", nullable: true),
        Assert\GreaterThanOrEqual(0, message: 'Zout moet groter of gelijk aan 0 zijn.'),
    ]
    protected ?float $salt = null;

    #[
        ORM\Column(type: "float", nullable: true),
        Assert\GreaterThanOrEqual(0, message: 'Vitamine A moet groter of gelijk aan 0 zijn.'),
    ]
    protected ?float $vitaminA = null;

    #[
        ORM\Column(type: "float", nullable: true),
        Assert\GreaterThanOrEqual(0, message: 'Vitamine B1 moet groter of gelijk aan 0 zijn.'),
    ]
    protected ?float $vitaminB1 = null;

    #[
        ORM\Column(type: "float", nullable: true),
        Assert\GreaterThanOrEqual(0, message: 'Vitamine B2 moet groter of gelijk aan 0 zijn.'),
    ]
    protected ?float $vitaminB2 = null;

    #[
        ORM\Column(type: "float", nullable: true),
        Assert\GreaterThanOrEqual(0, message: 'Vitamine B3 moet groter of gelijk aan 0 zijn.'),
    ]
    protected ?float $vitaminB3 = null;

    #[
        ORM\Column(type: "float", nullable: true),
        Assert\GreaterThanOrEqual(0, message: 'Vitamine B6 moet groter of gelijk aan 0 zijn.'),
    ]
    protected ?float $vitaminB6 = null;

    #[
        ORM\Column(type: "float", nullable: true),
        Assert\GreaterThanOrEqual(0, message: 'Vitamine B11 moet groter of gelijk aan 0 zijn.'),
    ]
    protected ?float $vitaminB11 = null;

    #[
        ORM\Column(type: "float", nullable: true),
        Assert\GreaterThanOrEqual(0, message: 'Vitamine B12 moet groter of gelijk aan 0 zijn.'),
    ]
    protected ?float $vitaminB12 = null;

    #[
        ORM\Column(type: "float", nullable: true),
        Assert\GreaterThanOrEqual(0, message: 'Vitamine C moet groter of gelijk aan 0 zijn.'),
    ]
    protected ?float $vitaminC = null;

    #[
        ORM\Column(type: "float", nullable: true),
        Assert\GreaterThanOrEqual(0, message: 'Vitamine D moet groter of gelijk aan 0 zijn.'),
    ]
    protected ?float $vitaminD = null;

    #[
        ORM\Column(type: "float", nullable: true),
        Assert\GreaterThanOrEqual(0, message: 'Vitamine E moet groter of gelijk aan 0 zijn.'),
    ]
    protected ?float $vitaminE = null;

    #[
        ORM\Column(type: "float", nullable: true),
        Assert\GreaterThanOrEqual(0, message: 'Vitamine K moet groter of gelijk aan 0 zijn.'),
    ]
    protected ?float $vitaminK = null;

    #[
        ORM\Column(type: "float", nullable: true),
        Assert\GreaterThanOrEqual(0, message: 'Kalium moet groter of gelijk aan 0 zijn.'),
    ]
    protected ?float $potassium = null;

    #[
        ORM\Column(type: "float", nullable: true),
        Assert\GreaterThanOrEqual(0, message: 'Calcium moet groter of gelijk aan 0 zijn.'),
    ]
    protected ?float $calcium = null;

    #[
        ORM\Column(type: "float", nullable: true),
        Assert\GreaterThanOrEqual(0, message: 'Fosfor moet groter of gelijk aan 0 zijn.'),
    ]
    protected ?float $phosphorus = null;

    #[
        ORM\Column(type: "float", nullable: true),
        Assert\GreaterThanOrEqual(0, message: 'IJzer moet groter of gelijk aan 0 zijn.'),
    ]
    protected ?float $iron = null;

    #[
        ORM\Column(type: "float", nullable: true),
        Assert\GreaterThanOrEqual(0, message: 'Magnesium moet groter of gelijk aan 0 zijn.'),
    ]
    protected ?float $magnesium = null;

    #[
        ORM\Column(type: "float", nullable: true),
        Assert\GreaterThanOrEqual(0, message: 'Koper moet groter of gelijk aan 0 zijn.'),
    ]
    protected ?float $copper = null;

    #[
        ORM\Column(type: "float", nullable: true),
        Assert\GreaterThanOrEqual(0, message: 'Zink moet groter of gelijk aan 0 zijn.'),
    ]
    protected ?float $zinc = null;

    #[
        ORM\Column(type: "float", nullable: true),
        Assert\GreaterThanOrEqual(0, message: 'Seleen moet groter of gelijk aan 0 zijn.'),
    ]
    protected ?float $selenium = null;

    #[
        ORM\Column(type: "float", nullable: true),
        Assert\GreaterThanOrEqual(0, message: 'Jodium moet groter of gelijk aan 0 zijn.'),
    ]
    protected ?float $iodine = null;

    #[
        ORM\Column(type: "float", nullable: true),
        Assert\GreaterThanOrEqual(0, message: 'Mangaan moet groter of gelijk aan 0 zijn.'),
    ]
    protected ?float $manganese = null;

    #[
        ORM\Column(type: "float", nullable: true),
        Assert\GreaterThanOrEqual(0, message: 'Molybdeen moet groter of gelijk aan 0 zijn.'),
    ]
    protected ?float $molybdenum = null;

    #[
        ORM\Column(type: "float", nullable: true),
        Assert\GreaterThanOrEqual(0, message: 'Chroom moet groter of gelijk aan 0 zijn.'),
    ]
    protected ?float $chromium = null;

    #[
        ORM\Column(type: "float", nullable: true),
        Assert\GreaterThanOrEqual(0, message: 'Fluoride moet groter of gelijk aan 0 zijn.'),
    ]
    protected ?float $fluoride = null;

    #[
        ORM\Column(type: "float", nullable: true),
        Assert\GreaterThanOrEqual(0, message: 'Alcohol moet groter of gelijk aan 0 zijn.'),
    ]
    protected ?float $alcohol = null;

    #[
        ORM\Column(type: "float", nullable: true),
        Assert\GreaterThanOrEqual(0, message: 'Cafeïne moet groter of gelijk aan 0 zijn.'),
    ]
    protected ?float $caffeine = null;

    #[ORM\OneToMany(targetEntity: "App\Entity\DayFoodstuffWeight", mappedBy: "foodstuff", cascade: ["remove"])]
    private Collection $dayFoodstuffWeights;

    #[ORM\OneToMany(targetEntity: "App\Entity\RecipeFoodstuffWeight", mappedBy: "foodstuff", cascade: ["remove"])]
    private Collection $recipeFoodstuffWeights;

    public function __construct()
    {
        $this->dayFoodstuffWeights = new ArrayCollection();
        $this->recipeFoodstuffWeights = new ArrayCollection();
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

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = strip_tags($name);
    }

    public function getPieceName(): ?string
    {
        return $this->pieceName;
    }

    public function setPieceName(?string $pieceName): void
    {
        $this->pieceName = $pieceName;
    }

    public function getPiecesName(): ?string
    {
        return $this->piecesName;
    }

    public function setPiecesName(?string $piecesName): void
    {
        $this->piecesName = $piecesName;
    }

    public function getPieceWeight(): ?float
    {
        return $this->pieceWeight;
    }

    public function setPieceWeight(?float $pieceWeight): void
    {
        $this->pieceWeight = $pieceWeight;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): void
    {
        $this->user = $user;
    }

    public function isLiquid(): bool
    {
        return $this->liquid;
    }

    public function setLiquid(bool $liquid): void
    {
        $this->liquid = $liquid;
    }

    public function getDensity(): ?float
    {
        return $this->density;
    }

    public function setDensity(?float $density): void
    {
        $this->density = $density;
    }

    public function getEnergy(): ?float
    {
        return $this->energy;
    }

    public function setEnergy(?float $energy): void
    {
        $this->energy = $energy;
    }

    public function getWater(): ?float
    {
        return $this->water;
    }

    public function setWater(?float $water): void
    {
        $this->water = $water;
    }

    public function getProtein(): ?float
    {
        return $this->protein;
    }

    public function setProtein(?float $protein): void
    {
        $this->protein = $protein;
    }

    public function getCarbohydrates(): ?float
    {
        return $this->carbohydrates;
    }

    public function setCarbohydrates(?float $carbohydrates): void
    {
        $this->carbohydrates = $carbohydrates;
    }

    public function getSucre(): ?float
    {
        return $this->sucre;
    }

    public function setSucre(?float $sucre): void
    {
        $this->sucre = $sucre;
    }

    public function getFat(): ?float
    {
        return $this->fat;
    }

    public function setFat(?float $fat): void
    {
        $this->fat = $fat;
    }

    public function getSaturatedFat(): ?float
    {
        return $this->saturatedFat;
    }

    public function setSaturatedFat(?float $saturatedFat): void
    {
        $this->saturatedFat = $saturatedFat;
    }

    public function getMonounsaturatedFat(): ?float
    {
        return $this->monounsaturatedFat;
    }

    public function setMonounsaturatedFat(?float $monounsaturatedFat): void
    {
        $this->monounsaturatedFat = $monounsaturatedFat;
    }

    public function getPolyunsaturatedFat(): ?float
    {
        return $this->polyunsaturatedFat;
    }

    public function setPolyunsaturatedFat(?float $polyunsaturatedFat): void
    {
        $this->polyunsaturatedFat = $polyunsaturatedFat;
    }

    public function getCholesterol(): ?float
    {
        return $this->cholesterol;
    }

    public function setCholesterol(?float $cholesterol): void
    {
        $this->cholesterol = $cholesterol;
    }

    public function getDietaryFiber(): ?float
    {
        return $this->dietaryFiber;
    }

    public function setDietaryFiber(?float $dietaryFiber): void
    {
        $this->dietaryFiber = $dietaryFiber;
    }

    public function getSalt(): ?float
    {
        return $this->salt;
    }

    public function setSalt(?float $salt): void
    {
        $this->salt = $salt;
    }

    public function getVitaminA(): ?float
    {
        return $this->vitaminA;
    }

    public function setVitaminA(?float $vitaminA): void
    {
        $this->vitaminA = $vitaminA;
    }

    public function getVitaminB1(): ?float
    {
        return $this->vitaminB1;
    }

    public function setVitaminB1(?float $vitaminB1): void
    {
        $this->vitaminB1 = $vitaminB1;
    }

    public function getVitaminB2(): ?float
    {
        return $this->vitaminB2;
    }

    public function setVitaminB2(?float $vitaminB2): void
    {
        $this->vitaminB2 = $vitaminB2;
    }

    public function getVitaminB3(): ?float
    {
        return $this->vitaminB3;
    }

    public function setVitaminB3(?float $vitaminB3): void
    {
        $this->vitaminB3 = $vitaminB3;
    }

    public function getVitaminB6(): ?float
    {
        return $this->vitaminB6;
    }

    public function setVitaminB6(?float $vitaminB6): void
    {
        $this->vitaminB6 = $vitaminB6;
    }

    public function getVitaminB11(): ?float
    {
        return $this->vitaminB11;
    }

    public function setVitaminB11(?float $vitaminB11): void
    {
        $this->vitaminB11 = $vitaminB11;
    }

    public function getVitaminB12(): ?float
    {
        return $this->vitaminB12;
    }

    public function setVitaminB12(?float $vitaminB12): void
    {
        $this->vitaminB12 = $vitaminB12;
    }

    public function getVitaminC(): ?float
    {
        return $this->vitaminC;
    }

    public function setVitaminC(?float $vitaminC): void
    {
        $this->vitaminC = $vitaminC;
    }

    public function getVitaminD(): ?float
    {
        return $this->vitaminD;
    }

    public function setVitaminD(?float $vitaminD): void
    {
        $this->vitaminD = $vitaminD;
    }

    public function getVitaminE(): ?float
    {
        return $this->vitaminE;
    }

    public function setVitaminE(?float $vitaminE): void
    {
        $this->vitaminE = $vitaminE;
    }

    public function getVitaminK(): ?float
    {
        return $this->vitaminK;
    }

    public function setVitaminK(?float $vitaminK): void
    {
        $this->vitaminK = $vitaminK;
    }

    public function getPotassium(): ?float
    {
        return $this->potassium;
    }

    public function setPotassium(?float $potassium): void
    {
        $this->potassium = $potassium;
    }

    public function getCalcium(): ?float
    {
        return $this->calcium;
    }

    public function setCalcium(?float $calcium): void
    {
        $this->calcium = $calcium;
    }

    public function getPhosphorus(): ?float
    {
        return $this->phosphorus;
    }

    public function setPhosphorus(?float $phosphorus): void
    {
        $this->phosphorus = $phosphorus;
    }

    public function getIron(): ?float
    {
        return $this->iron;
    }

    public function setIron(?float $iron): void
    {
        $this->iron = $iron;
    }

    public function getMagnesium(): ?float
    {
        return $this->magnesium;
    }

    public function setMagnesium(?float $magnesium): void
    {
        $this->magnesium = $magnesium;
    }

    public function getCopper(): ?float
    {
        return $this->copper;
    }

    public function setCopper(?float $copper): void
    {
        $this->copper = $copper;
    }

    public function getZinc(): ?float
    {
        return $this->zinc;
    }

    public function setZinc(?float $zinc): void
    {
        $this->zinc = $zinc;
    }

    public function getSelenium(): ?float
    {
        return $this->selenium;
    }

    public function setSelenium(?float $selenium): void
    {
        $this->selenium = $selenium;
    }

    public function getIodine(): ?float
    {
        return $this->iodine;
    }

    public function setIodine(?float $iodine): void
    {
        $this->iodine = $iodine;
    }

    public function getManganese(): ?float
    {
        return $this->manganese;
    }

    public function setManganese(?float $manganese): void
    {
        $this->manganese = $manganese;
    }

    public function getMolybdenum(): ?float
    {
        return $this->molybdenum;
    }

    public function setMolybdenum(?float $molybdenum): void
    {
        $this->molybdenum = $molybdenum;
    }

    public function getChromium(): ?float
    {
        return $this->chromium;
    }

    public function setChromium(?float $chromium): void
    {
        $this->chromium = $chromium;
    }

    public function getFluoride(): ?float
    {
        return $this->fluoride;
    }

    public function setFluoride(?float $fluoride): void
    {
        $this->fluoride = $fluoride;
    }

    public function getAlcohol(): ?float
    {
        return $this->alcohol;
    }

    public function setAlcohol(?float $alcohol): void
    {
        $this->alcohol = $alcohol;
    }

    public function getCaffeine(): ?float
    {
        return $this->caffeine;
    }

    public function setCaffeine(?float $caffeine): void
    {
        $this->caffeine = $caffeine;
    }

    public function getSodium(): ?float
    {
        if (is_null($this->salt)) {
            return null;
        }

        return $this->salt * 400;
    }

    public function getDayFoodstuffWeights(): Collection
    {
        return $this->dayFoodstuffWeights;
    }

    public function setDayFoodstuffWeights(Collection $foodstuffWeights): void
    {
        foreach ($foodstuffWeights as $foodstuffWeight) {
            $foodstuffWeight->setFoodstuff($this);
        }
        $this->dayFoodstuffWeights = $foodstuffWeights;
    }

    public function getRecipeFoodstuffWeights(): Collection
    {
        return $this->recipeFoodstuffWeights;
    }

    public function setRecipeFoodstuffWeights(Collection $foodstuffWeights): void
    {
        foreach ($foodstuffWeights as $foodstuffWeight) {
            $foodstuffWeight->setFoodstuff($this);
        }
        $this->recipeFoodstuffWeights = $foodstuffWeights;
    }
}
