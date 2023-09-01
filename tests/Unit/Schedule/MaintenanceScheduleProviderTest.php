<?php

declare(strict_types=1);

namespace App\Tests\Unit\Schedule;

use App\Schedule\MaintenanceScheduleProvider;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Scheduler\Schedule;

#[CoversClass(MaintenanceScheduleProvider::class)]
class MaintenanceScheduleProviderTest extends TestCase
{
    public function testGetSchedule(): void
    {
        $subject = new MaintenanceScheduleProvider();
        self::assertInstanceOf(Schedule::class, $subject->getSchedule());
    }
}