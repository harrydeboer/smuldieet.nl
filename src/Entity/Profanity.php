<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ProfanityRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ORM\Entity(repositoryClass: ProfanityRepository::class),
    ORM\Table(name: "profanity"),
]
class Profanity
{
    #[
        ORM\Id,
        ORM\Column(type: "integer"),
        ORM\GeneratedValue(strategy: "IDENTITY"),
    ]
    private int $id;

    #[
        ORM\Column(type: "string"),
        Assert\Length(min: 1, max: 255, minMessage: 'De naam mag niet leeg zijn.',
            maxMessage: 'De naam mag niet meer dan 255 tekens hebben.'),
    ]
    private string $name;

    public function getId(): ?int
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
        $this->name = $name;
    }
}
