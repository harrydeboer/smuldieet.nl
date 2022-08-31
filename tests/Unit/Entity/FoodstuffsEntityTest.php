<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\Day;
use App\Entity\Recipe;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class FoodstuffsEntityTest extends TestCase
{
    public function testRoundToNearest()
    {
        $day = new Day();
        $collection = new ArrayCollection();
        $this->assertEquals(0.25, $day->roundToNearest(0.1, $collection, 1)[1]);
        $this->assertEquals(0.25, $day->roundToNearest(0.2, $collection, 1)[1]);
        $this->assertEquals(0.5, $day->roundToNearest(0.4, $collection, 1)[1]);
        $this->assertEquals(0.75, $day->roundToNearest(0.8, $collection, 1)[1]);
        $recipe = new Recipe();
        $this->assertEquals(1, $recipe->roundToNearest(1, $collection, 1)[1]);
        $this->assertEquals(1.5, $recipe->roundToNearest(1.4, $collection, 1)[1]);
        $this->assertEquals(2, $recipe->roundToNearest(1.8, $collection, 1)[1]);
        $this->assertEquals(3, $recipe->roundToNearest(3, $collection, 1)[1]);
        $this->assertEquals(10, $recipe->roundToNearest(10, $collection, 1)[1]);
        $this->assertEquals(20, $recipe->roundToNearest(20, $collection, 1)[1]);
    }
}
