<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Profanity;
use Doctrine\Persistence\ObjectManager;

class ProfanityFixture extends AbstractFixture
{
    public function load(ObjectManager $manager): void
    {
        $profanity = new Profanity();
        $profanity->setName('badBadBad');
        $manager->persist($profanity);

        $manager->flush();
    }
}
