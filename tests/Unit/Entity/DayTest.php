<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\Day;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class DayTest extends TestCase
{
    public function testRoundToNearest()
    {
        $day = new Day();
        $collection = new ArrayCollection();
        $this->assertEquals(1, $day->roundToNearest(0.1, $collection, 1)[1]);
        $this->assertEquals(1, $day->roundToNearest(0.2, $collection, 1)[1]);
        $this->assertEquals(2, $day->roundToNearest(0.4, $collection, 1)[1]);
        $this->assertEquals(3, $day->roundToNearest(0.8, $collection, 1)[1]);
        $this->assertEquals(4, $day->roundToNearest(1, $collection, 1)[1]);
        $this->assertEquals(6, $day->roundToNearest(1.4, $collection, 1)[1]);
        $this->assertEquals(8, $day->roundToNearest(1.8, $collection, 1)[1]);
        $this->assertEquals(12, $day->roundToNearest(3, $collection, 1)[1]);
        $this->assertEquals(40, $day->roundToNearest(10, $collection, 1)[1]);
        $this->assertEquals(80, $day->roundToNearest(20, $collection, 1)[1]);
    }
}
