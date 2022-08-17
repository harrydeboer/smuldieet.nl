<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Entity\Foodstuff;
use App\Service\WeightsCorrectionService;
use PHPUnit\Framework\TestCase;

class WeightsCorrectionServiceTest extends TestCase
{
    public function testCorrectArray()
    {
        $foodstuff = new Foodstuff();
        $foodstuff->setId(3);
        $this->assertEquals(serialize(['3' => 4]),
            WeightsCorrectionService::correctArray(['33' => null, '0' => 4], [$foodstuff->getId()]));
    }
}
