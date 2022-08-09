<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Service\DateCheckService;
use PHPUnit\Framework\TestCase;

class DateCheckServiceTest extends TestCase
{
    public function testCheckDate()
    {
        $this->assertTrue(DateCheckService::checkDate('10-02-2000'));
        $this->assertFalse(DateCheckService::checkDate('10-02-200'));
        $this->assertFalse(DateCheckService::checkDate('10-02-9999', true));
    }
}
