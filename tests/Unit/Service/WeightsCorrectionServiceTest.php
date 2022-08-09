<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Service\DateCheckService;
use App\Service\WeightsCorrectionService;
use PHPUnit\Framework\TestCase;

class WeightsCorrectionServiceTest extends TestCase
{
    public function testCorrectArray()
    {
        $this->assertEquals(serialize(['18' => 3]), WeightsCorrectionService::correctArray(['18' => null, '0' => 3]));
    }
}
