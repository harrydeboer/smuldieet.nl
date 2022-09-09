<?php

declare(strict_types=1);

namespace App\Entity;

use App\ValueObject\Nutrient;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use JetBrains\PhpStorm\Pure;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\FoodstuffRepository;

#[
    ORM\Entity(repositoryClass: FoodstuffRepository::class),
    ORM\Table(name: "foodstuff"),
    ORM\UniqueConstraint(name: "name_unique", columns: ["user_id", "name"]),
    UniqueEntity(fields: ["user", "name"], message: "Er is al een voedingsmiddel met deze naam."),
]
class Foodstuff
{
    public static array $foodstuffChoicesArray = [
        '¼' => 0.25,
        '½' => 0.5,
        '¾' => 0.75,
        '1' => 1,
        '1½' => 1.5,
        '2' => 2,
        '3' => 3,
        '4' => 4,
        '5' => 5,
        '6' => 6,
        '7' => 7,
        '8' => 8,
        '9' => 9,
        '10' => 10,
        '11' => 11,
        '12' => 12,
        '13' => 13,
        '14' => 14,
        '15' => 15,
        '16' => 16,
        '17' => 17,
        '18' => 18,
        '19' => 19,
        '20' => 20,
    ];

    #[
        ORM\Id,
        ORM\Column(type: "integer"),
        ORM\GeneratedValue(strategy: "IDENTITY"),
    ]
    private int $id;

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
        ORM\Column(type: "float", nullable: true),
        Assert\GreaterThanOrEqual(0, message: 'Gewicht per stuk moet groter of gelijk aan 0 zijn.'),
    ]
    private ?float $pieceWeight = null;

    #[
        ORM\Column(type: "float", nullable: true),
        Assert\GreaterThanOrEqual(0, message: 'Energie kcal moet groter of gelijk aan 0 zijn.'),
    ]
    private ?float $energyKcal = null;

    #[
        ORM\Column(type: "float", nullable: true),
        Assert\GreaterThanOrEqual(0, message: 'Water moet groter of gelijk aan 0 zijn.'),
    ]
    private ?float $water = null;

    #[
        ORM\Column(type: "float", nullable: true),
        Assert\GreaterThanOrEqual(0, message: 'Eiwit moet groter of gelijk aan 0 zijn.'),
    ]
    private ?float $protein = null;

    #[
        ORM\Column(type: "float", nullable: true),
        Assert\GreaterThanOrEqual(0, message: 'Koolhydraten moet groter of gelijk aan 0 zijn.'),
    ]
    private ?float $carbohydrates = null;

    #[
        ORM\Column(type: "float", nullable: true),
        Assert\GreaterThanOrEqual(0, message: 'Suiker moet groter of gelijk aan 0 zijn.'),
    ]
    private ?float $sucre = null;

    #[
        ORM\Column(type: "float", nullable: true),
        Assert\GreaterThanOrEqual(0, message: 'Vet moet groter of gelijk aan 0 zijn.'),
    ]
    private ?float $fat = null;

    #[
        ORM\Column(type: "float", nullable: true),
        Assert\GreaterThanOrEqual(0, message: 'Verzadigd vet moet groter of gelijk aan 0 zijn.'),
    ]
    private ?float $saturatedFat = null;

    #[
        ORM\Column(type: "float", nullable: true),
        Assert\GreaterThanOrEqual(0, message: 'Enkelvoudig verzadigd vet moet groter of gelijk aan 0 zijn.'),
    ]
    private ?float $monounsaturatedFat = null;

    #[
        ORM\Column(type: "float", nullable: true),
        Assert\GreaterThanOrEqual(0, message: 'Meervoudig verzadigd vet moet groter of gelijk aan 0 zijn.'),
    ]
    private ?float $polyunsaturatedFat = null;

    #[
        ORM\Column(type: "float", nullable: true),
        Assert\GreaterThanOrEqual(0, message: 'Cholesterol moet groter of gelijk aan 0 zijn.'),
    ]
    private ?float $cholesterol = null;

    #[
        ORM\Column(type: "float", nullable: true),
        Assert\GreaterThanOrEqual(0, message: 'Voedingsvezel moet groter of gelijk aan 0 zijn.'),
    ]
    private ?float $dietaryFiber = null;

    #[
        ORM\Column(type: "float", nullable: true),
        Assert\GreaterThanOrEqual(0, message: 'Zout moet groter of gelijk aan 0 zijn.'),
    ]
    private ?float $salt = null;

    #[
        ORM\Column(type: "float", nullable: true),
        Assert\GreaterThanOrEqual(0, message: 'Vitamine A moet groter of gelijk aan 0 zijn.'),
    ]
    private ?float $vitaminA = null;

    #[
        ORM\Column(type: "float", nullable: true),
        Assert\GreaterThanOrEqual(0, message: 'Vitamine B1 moet groter of gelijk aan 0 zijn.'),
    ]
    private ?float $vitaminB1 = null;

    #[
        ORM\Column(type: "float", nullable: true),
        Assert\GreaterThanOrEqual(0, message: 'Vitamine B2 moet groter of gelijk aan 0 zijn.'),
    ]
    private ?float $vitaminB2 = null;

    #[
        ORM\Column(type: "float", nullable: true),
        Assert\GreaterThanOrEqual(0, message: 'Vitamine B3 moet groter of gelijk aan 0 zijn.'),
    ]
    private ?float $vitaminB3 = null;

    #[
        ORM\Column(type: "float", nullable: true),
        Assert\GreaterThanOrEqual(0, message: 'Vitamine B6 moet groter of gelijk aan 0 zijn.'),
    ]
    private ?float $vitaminB6 = null;

    #[
        ORM\Column(type: "float", nullable: true),
        Assert\GreaterThanOrEqual(0, message: 'Vitamine B11 moet groter of gelijk aan 0 zijn.'),
    ]
    private ?float $vitaminB11 = null;

    #[
        ORM\Column(type: "float", nullable: true),
        Assert\GreaterThanOrEqual(0, message: 'Vitamine B12 moet groter of gelijk aan 0 zijn.'),
    ]
    private ?float $vitaminB12 = null;

    #[
        ORM\Column(type: "float", nullable: true),
        Assert\GreaterThanOrEqual(0, message: 'Vitamine C moet groter of gelijk aan 0 zijn.'),
    ]
    private ?float $vitaminC = null;

    #[
        ORM\Column(type: "float", nullable: true),
        Assert\GreaterThanOrEqual(0, message: 'Vitamine D moet groter of gelijk aan 0 zijn.'),
    ]
    private ?float $vitaminD = null;

    #[
        ORM\Column(type: "float", nullable: true),
        Assert\GreaterThanOrEqual(0, message: 'Vitamine E moet groter of gelijk aan 0 zijn.'),
    ]
    private ?float $vitaminE = null;

    #[
        ORM\Column(type: "float", nullable: true),
        Assert\GreaterThanOrEqual(0, message: 'Vitamine K moet groter of gelijk aan 0 zijn.'),
    ]
    private ?float $vitaminK = null;

    #[
        ORM\Column(type: "float", nullable: true),
        Assert\GreaterThanOrEqual(0, message: 'Kalium moet groter of gelijk aan 0 zijn.'),
    ]
    private ?float $potassium = null;

    #[
        ORM\Column(type: "float", nullable: true),
        Assert\GreaterThanOrEqual(0, message: 'Calcium moet groter of gelijk aan 0 zijn.'),
    ]
    private ?float $calcium = null;

    #[
        ORM\Column(type: "float", nullable: true),
        Assert\GreaterThanOrEqual(0, message: 'Fosfor moet groter of gelijk aan 0 zijn.'),
    ]
    private ?float $phosphorus = null;

    #[
        ORM\Column(type: "float", nullable: true),
        Assert\GreaterThanOrEqual(0, message: 'IJzer moet groter of gelijk aan 0 zijn.'),
    ]
    private ?float $iron = null;

    #[
        ORM\Column(type: "float", nullable: true),
        Assert\GreaterThanOrEqual(0, message: 'Magnesium moet groter of gelijk aan 0 zijn.'),
    ]
    private ?float $magnesium = null;

    #[
        ORM\Column(type: "float", nullable: true),
        Assert\GreaterThanOrEqual(0, message: 'Koper moet groter of gelijk aan 0 zijn.'),
    ]
    private ?float $copper = null;

    #[
        ORM\Column(type: "float", nullable: true),
        Assert\GreaterThanOrEqual(0, message: 'Zink moet groter of gelijk aan 0 zijn.'),
    ]
    private ?float $zinc = null;

    #[
        ORM\Column(type: "float", nullable: true),
        Assert\GreaterThanOrEqual(0, message: 'Seleen moet groter of gelijk aan 0 zijn.'),
    ]
    private ?float $selenium = null;

    #[
        ORM\Column(type: "float", nullable: true),
        Assert\GreaterThanOrEqual(0, message: 'Jodium moet groter of gelijk aan 0 zijn.'),
    ]
    private ?float $iodine = null;

    #[
        ORM\Column(type: "float", nullable: true),
        Assert\GreaterThanOrEqual(0, message: 'Mangaan moet groter of gelijk aan 0 zijn.'),
    ]
    private ?float $manganese = null;

    #[
        ORM\Column(type: "float", nullable: true),
        Assert\GreaterThanOrEqual(0, message: 'Molybdeen moet groter of gelijk aan 0 zijn.'),
    ]
    private ?float $molybdenum = null;

    #[
        ORM\Column(type: "float", nullable: true),
        Assert\GreaterThanOrEqual(0, message: 'Chroom moet groter of gelijk aan 0 zijn.'),
    ]
    private ?float $chromium = null;

    #[
        ORM\Column(type: "float", nullable: true),
        Assert\GreaterThanOrEqual(0, message: 'Fluoride moet groter of gelijk aan 0 zijn.'),
    ]
    private ?float $fluoride = null;

    #[
        ORM\Column(type: "float", nullable: true),
        Assert\GreaterThanOrEqual(0, message: 'Alcohol moet groter of gelijk aan 0 zijn.'),
    ]
    private ?float $alcohol = null;

    #[
        ORM\Column(type: "float", nullable: true),
        Assert\GreaterThanOrEqual(0, message: 'Caffeïne moet groter of gelijk aan 0 zijn.'),
    ]
    private ?float $caffeine = null;

    #[ORM\ManyToMany(targetEntity: "Day", mappedBy: "foodstuffs")]
    private Collection $days;

    #[ORM\ManyToMany(targetEntity: "Recipe", mappedBy: "foodstuffs")]
    private Collection $recipes;

    #[Pure] public function __construct()
    {
        $this->days = new ArrayCollection();
        $this->recipes = new ArrayCollection();
    }

    public function getDays(): Collection
    {
        return $this->days;
    }

    public function setDays(ArrayCollection $days): void
    {
        $this->days = $days;
    }

    public function addDay(Day $day): void
    {
        if ($this->days->contains($day)) {
            return;
        }

        $this->days->add($day);
        $day->addFoodstuff($this);
    }

    public function removeDay(Day $day): void
    {
        if (!$this->days->contains($day)) {
            return;
        }

        $this->days->removeElement($day);
        $day->removeFoodstuff($this);
    }

    public function getRecipes(): Collection
    {
        return $this->recipes;
    }

    public function setRecipes(Collection $recipes): void
    {
        $this->recipes = $recipes;
    }

    public function addRecipe(Recipe $recipe): void
    {
        if ($this->recipes->contains($recipe)) {
            return;
        }

        $this->recipes->add($recipe);
        $recipe->addFoodstuff($this);
    }

    public function removeRecipe(Recipe $recipe): void
    {
        if (!$this->recipes->contains($recipe)) {
            return;
        }

        $this->recipes->removeElement($recipe);
        $recipe->removeFoodstuff($this);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
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

    public function getEnergyKcal(): ?float
    {
        return $this->energyKcal;
    }

    public function setEnergyKcal(?float $energyKcal): void
    {
        $this->energyKcal = $energyKcal;
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

    public function getEnergyKJ(): ?float
    {
        if (is_null($this->energyKcal)) {
            return null;
        }

        return $this->energyKcal * 4.184;
    }

    public function getSodium(): ?float
    {
        if (is_null($this->salt)) {
            return null;
        }

        return $this->salt * 400;
    }

    /**
     * @return Nutrient[]
     */
    public static function getNutrients(
        string $camelOrSnake = 'camel',
        DateTime $birthdate = null,
        string $gender = 'man',
        float $weight = 70,
    ): array
    {
        if ($weight <= 0) {
            throw new InvalidArgumentException('Weight has to be greater than 0');
        } elseif ($weight > 1000) {
            throw new InvalidArgumentException('Weight has to be smaller than 1000');
        }

        if (!in_array($gender, User::GENDER)) {
            throw new InvalidArgumentException('Gender has to be man or vrouw.');
        }

        if ($gender === 'vrouw') {
            $factor = 0.8;
        } else {
            $factor = 1;
        }

        $nutrients = [];

        $nutrient = new Nutrient();
        $nutrient->setName('energyKcal');
        $nutrient->setNameNL('Energie');
        $nutrient->setMinRDA(2500 * $factor);
        $nutrient->setMaxRDA(2500 * $factor);
        $nutrient->setUnit('kcal');
        $nutrient->setDecimalPlaces(0);
        $nutrients[$nutrient->getName()] = $nutrient;

        $nutrient = new Nutrient();
        $nutrient->setName('water');
        $nutrient->setNameNL('Water');
        $nutrient->setMinRDA(35 * $weight);
        $nutrient->setMaxRDA(35 * $weight);
        $nutrient->setUnit('ml');
        $nutrient->setDecimalPlaces(1);
        $nutrients[$nutrient->getName()] = $nutrient;

        $nutrient = new Nutrient();
        $nutrient->setName('protein');
        $nutrient->setNameNL('Eiwit');
        $nutrient->setMinRDA(0.8 * $weight);
        $nutrient->setMaxRDA(0.8 * $weight);
        $nutrient->setUnit('g');
        $nutrient->setDecimalPlaces(1);
        $nutrients[$nutrient->getName()] = $nutrient;

        $nutrient = new Nutrient();
        $nutrient->setName('carbohydrates');
        $nutrient->setNameNL('Koolhydraten');
        $nutrient->setMinRDA(250 * $factor);
        $nutrient->setMaxRDA(438 * $factor);
        $nutrient->setUnit('g');
        $nutrient->setDecimalPlaces(1);
        $nutrients[$nutrient->getName()] = $nutrient;

        $nutrient = new Nutrient();
        $nutrient->setName('sucre');
        $nutrient->setNameNL('Suiker');
        $nutrient->setMinRDA(60 * $factor);
        $nutrient->setMaxRDA(90 * $factor);
        $nutrient->setUnit('g');
        $nutrient->setDecimalPlaces(1);
        $nutrients[$nutrient->getName()] = $nutrient;

        $nutrient = new Nutrient();
        $nutrient->setName('fat');
        $nutrient->setNameNL('Vet');
        $nutrient->setMinRDA(56 * $factor);
        $nutrient->setMaxRDA(111 * $factor);
        $nutrient->setUnit('g');
        $nutrient->setDecimalPlaces(1);
        $nutrients[$nutrient->getName()] = $nutrient;

        $nutrient = new Nutrient();
        $nutrient->setName('saturatedFat');
        $nutrient->setNameNL('Verzadigd vet');
        $nutrient->setMinRDA(8 * $factor);
        $nutrient->setMaxRDA(28 * $factor);
        $nutrient->setUnit('g');
        $nutrient->setDecimalPlaces(1);
        $nutrients[$nutrient->getName()] = $nutrient;

        $nutrient = new Nutrient();
        $nutrient->setName('monounsaturatedFat');
        $nutrient->setNameNL('Enkelvoudig verzadigd vet');
        $nutrient->setMinRDA(4 * $factor);
        $nutrient->setMaxRDA(16 * $factor);
        $nutrient->setUnit('g');
        $nutrient->setDecimalPlaces(1);
        $nutrients[$nutrient->getName()] = $nutrient;

        $nutrient = new Nutrient();
        $nutrient->setName('polyunsaturatedFat');
        $nutrient->setNameNL('Meervoudig verzadigd vet');
        $nutrient->setMinRDA(4 * $factor);
        $nutrient->setMaxRDA(17 * $factor);
        $nutrient->setUnit('g');
        $nutrient->setDecimalPlaces(1);
        $nutrients[$nutrient->getName()] = $nutrient;

        $nutrient = new Nutrient();
        $nutrient->setName('cholesterol');
        $nutrient->setNameNL('Cholesterol');
        $nutrient->setMinRDA(0);
        $nutrient->setMaxRDA(250);
        $nutrient->setUnit('mg');
        $nutrient->setDecimalPlaces(1);
        $nutrients[$nutrient->getName()] = $nutrient;

        $nutrient = new Nutrient();
        $nutrient->setName('dietaryFiber');
        $nutrient->setNameNL('Vezels');
        $nutrient->setMinRDA(30);
        $nutrient->setMaxRDA(null);
        $nutrient->setUnit('mg');
        $nutrient->setDecimalPlaces(1);
        $nutrients[$nutrient->getName()] = $nutrient;

        $nutrient = new Nutrient();
        $nutrient->setName('salt');
        $nutrient->setNameNL('Zout');
        $nutrient->setMinRDA(0);
        $nutrient->setMaxRDA(6);
        $nutrient->setUnit('g');
        $nutrient->setDecimalPlaces(1);
        $nutrients[$nutrient->getName()] = $nutrient;

        $nutrient = new Nutrient();
        $nutrient->setName('vitaminA');
        $nutrient->setNameNL('Vitamine A');
        $nutrient->setMinRDA(0.8);
        $nutrient->setMaxRDA(3);
        $nutrient->setUnit('mg');
        $nutrient->setDecimalPlaces(2);
        $nutrients[$nutrient->getName()] = $nutrient;

        $nutrient = new Nutrient();
        $nutrient->setName('vitaminB1');
        $nutrient->setNameNL('Vitamine B1');
        $nutrient->setMinRDA(0.9);
        $nutrient->setMaxRDA(6);
        $nutrient->setUnit('mg');
        $nutrient->setDecimalPlaces(2);
        $nutrients[$nutrient->getName()] = $nutrient;

        $nutrient = new Nutrient();
        $nutrient->setName('vitaminB2');
        $nutrient->setNameNL('Vitamine B2');
        $nutrient->setMinRDA(1.6);
        $nutrient->setMaxRDA(null);
        $nutrient->setUnit('mg');
        $nutrient->setDecimalPlaces(2);
        $nutrients[$nutrient->getName()] = $nutrient;

        $nutrient = new Nutrient();
        $nutrient->setName('vitaminB3');
        $nutrient->setNameNL('Vitamine B3');
        $nutrient->setMinRDA(17);
        $nutrient->setMaxRDA(900);
        $nutrient->setUnit('mg');
        $nutrient->setDecimalPlaces(2);
        $nutrients[$nutrient->getName()] = $nutrient;

        $nutrient = new Nutrient();
        $nutrient->setName('vitaminB6');
        $nutrient->setNameNL('Vitamine B6');
        $nutrient->setMinRDA(1.5);
        $nutrient->setMaxRDA(21);
        $nutrient->setUnit('mg');
        $nutrient->setDecimalPlaces(2);
        $nutrients[$nutrient->getName()] = $nutrient;

        $nutrient = new Nutrient();
        $nutrient->setName('vitaminB11');
        $nutrient->setNameNL('Vitamine B11');
        $nutrient->setMinRDA(300);
        $nutrient->setMaxRDA(1000);
        $nutrient->setUnit('μg');
        $nutrient->setDecimalPlaces(2);
        $nutrients[$nutrient->getName()] = $nutrient;

        $nutrient = new Nutrient();
        $nutrient->setName('vitaminB12');
        $nutrient->setNameNL('Vitamine B12');
        $nutrient->setMinRDA(2.8);
        $nutrient->setMaxRDA(null);
        $nutrient->setUnit('μg');
        $nutrient->setDecimalPlaces(2);
        $nutrients[$nutrient->getName()] = $nutrient;

        $nutrient = new Nutrient();
        $nutrient->setName('vitaminC');
        $nutrient->setNameNL('Vitamine C');
        $nutrient->setMinRDA(75);
        $nutrient->setMaxRDA(2000);
        $nutrient->setUnit('mg');
        $nutrient->setDecimalPlaces(2);
        $nutrients[$nutrient->getName()] = $nutrient;

        $nutrient = new Nutrient();
        $nutrient->setName('vitaminD');
        $nutrient->setNameNL('Vitamine D');
        $nutrient->setMinRDA(10);
        /**
         * For people over 70 years the vitamin D minimum is 20 μg.
         */
        if (!is_null($birthdate)) {
            if ((time() - $birthdate->getTimestamp()) / 24 / 60 / 60 / 365.25 >= 70) {
                $nutrient->setMinRDA(20);
            }
        }
        $nutrient->setMaxRDA(50);
        $nutrient->setUnit('μg');
        $nutrient->setDecimalPlaces(2);
        $nutrients[$nutrient->getName()] = $nutrient;

        $nutrient = new Nutrient();
        $nutrient->setName('vitaminE');
        $nutrient->setNameNL('Vitamine E');
        $nutrient->setMinRDA(11);
        $nutrient->setMaxRDA(300);
        $nutrient->setUnit('mg');
        $nutrient->setDecimalPlaces(2);
        $nutrients[$nutrient->getName()] = $nutrient;

        $nutrient = new Nutrient();
        $nutrient->setName('vitaminK');
        $nutrient->setNameNL('Vitamine K');
        $nutrient->setMinRDA(70);
        $nutrient->setMaxRDA(null);
        $nutrient->setUnit('μg');
        $nutrient->setDecimalPlaces(2);
        $nutrients[$nutrient->getName()] = $nutrient;

        $nutrient = new Nutrient();
        $nutrient->setName('potassium');
        $nutrient->setNameNL('Kalium');
        $nutrient->setMinRDA(3500);
        $nutrient->setMaxRDA(3500);
        $nutrient->setUnit('mg');
        $nutrient->setDecimalPlaces(0);
        $nutrients[$nutrient->getName()] = $nutrient;

        $nutrient = new Nutrient();
        $nutrient->setName('calcium');
        $nutrient->setNameNL('Calcium');
        $nutrient->setMinRDA(1000);
        $nutrient->setMaxRDA(2500);
        $nutrient->setUnit('mg');
        $nutrient->setDecimalPlaces(0);
        $nutrients[$nutrient->getName()] = $nutrient;

        $nutrient = new Nutrient();
        $nutrient->setName('phosphorus');
        $nutrient->setNameNL('Fosfor');
        $nutrient->setMinRDA(550);
        $nutrient->setMaxRDA(3000);
        $nutrient->setUnit('mg');
        $nutrient->setDecimalPlaces(0);
        $nutrients[$nutrient->getName()] = $nutrient;

        $nutrient = new Nutrient();
        $nutrient->setName('iron');
        $nutrient->setNameNL('IJzer');
        $nutrient->setMinRDA(11);
        $nutrient->setMaxRDA(45);
        $nutrient->setUnit('mg');
        $nutrient->setDecimalPlaces(1);
        $nutrients[$nutrient->getName()] = $nutrient;

        $nutrient = new Nutrient();
        $nutrient->setName('magnesium');
        $nutrient->setNameNL('Magnesium');
        if ($gender === 'vrouw') {
            $nutrient->setMinRDA(300);
        } else {
            $nutrient->setMinRDA(350);
        }
        $nutrient->setMaxRDA(650);
        $nutrient->setUnit('mg');
        $nutrient->setDecimalPlaces(0);
        $nutrients[$nutrient->getName()] = $nutrient;

        $nutrient = new Nutrient();
        $nutrient->setName('copper');
        $nutrient->setNameNL('Koper');
        $nutrient->setMinRDA(0.9);
        $nutrient->setMaxRDA(6);
        $nutrient->setUnit('mg');
        $nutrient->setDecimalPlaces(2);
        $nutrients[$nutrient->getName()] = $nutrient;

        $nutrient = new Nutrient();
        $nutrient->setName('zinc');
        $nutrient->setNameNL('Zink');
        if ($gender === 'vrouw') {
            $nutrient->setMinRDA(7);
        } else {
            $nutrient->setMinRDA(9);
        }
        $nutrient->setMaxRDA(25);
        $nutrient->setUnit('mg');
        $nutrient->setDecimalPlaces(2);
        $nutrients[$nutrient->getName()] = $nutrient;

        $nutrient = new Nutrient();
        $nutrient->setName('selenium');
        $nutrient->setNameNL('Seleen');
        $nutrient->setMinRDA(70);
        $nutrient->setMaxRDA(300);
        $nutrient->setUnit('μg');
        $nutrient->setDecimalPlaces(0);
        $nutrients[$nutrient->getName()] = $nutrient;

        $nutrient = new Nutrient();
        $nutrient->setName('iodine');
        $nutrient->setNameNL('Jodium');
        $nutrient->setMinRDA(150);
        $nutrient->setMaxRDA(600);
        $nutrient->setUnit('μg');
        $nutrient->setDecimalPlaces(0);
        $nutrients[$nutrient->getName()] = $nutrient;

        $nutrient = new Nutrient();
        $nutrient->setName('manganese');
        $nutrient->setNameNL('Mangaan');
        $nutrient->setMinRDA(3);
        $nutrient->setMaxRDA(11);
        $nutrient->setUnit('mg');
        $nutrient->setDecimalPlaces(2);
        $nutrients[$nutrient->getName()] = $nutrient;

        $nutrient = new Nutrient();
        $nutrient->setName('molybdenum');
        $nutrient->setNameNL('Molybdeen');
        $nutrient->setMinRDA(65);
        $nutrient->setMaxRDA(600);
        $nutrient->setUnit('μg');
        $nutrient->setDecimalPlaces(0);
        $nutrients[$nutrient->getName()] = $nutrient;

        $nutrient = new Nutrient();
        $nutrient->setName('chromium');
        $nutrient->setNameNL('Chroom');
        $nutrient->setMinRDA(30);
        $nutrient->setMaxRDA(250);
        $nutrient->setUnit('μg');
        $nutrient->setDecimalPlaces(0);
        $nutrients[$nutrient->getName()] = $nutrient;

        $nutrient = new Nutrient();
        $nutrient->setName('fluoride');
        $nutrient->setNameNL('Fluoride');
        $nutrient->setMinRDA(0);
        $nutrient->setMaxRDA(7);
        $nutrient->setUnit('mg');
        $nutrient->setDecimalPlaces(2);
        $nutrients[$nutrient->getName()] = $nutrient;

        $nutrient = new Nutrient();
        $nutrient->setName('alcohol');
        $nutrient->setNameNL('Alcohol');
        $nutrient->setMinRDA(0);
        $nutrient->setMaxRDA(10);
        $nutrient->setUnit('g');
        $nutrient->setDecimalPlaces(0);
        $nutrients[$nutrient->getName()] = $nutrient;

        $nutrient = new Nutrient();
        $nutrient->setName('caffeine');
        $nutrient->setNameNL('Caffeïne');
        $nutrient->setMinRDA(0);
        $nutrient->setMaxRDA(400);
        $nutrient->setUnit('mg');
        $nutrient->setDecimalPlaces(0);
        $nutrients[$nutrient->getName()] = $nutrient;

        if ($camelOrSnake === 'snake') {
            $arraySnake = [];
            foreach ($nutrients as $key => $nutrient) {
                $arraySnake[strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $key))] = $nutrient;
            }

            return $arraySnake;
        }

        return $nutrients;
    }
}
