<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Tag;
use Doctrine\Persistence\ObjectManager;

class TagFixture extends AbstractFixture
{
    public function load(ObjectManager $manager): void
    {
        $tag = new Tag();
        $tag->setName('test');
        $tag->setCreatedAt(time());
        $manager->persist($tag);

        $manager->flush();
    }
}
