<?php

declare(strict_types=1);

namespace App\Tests\Unit\Schedule;

use App\Schedule\MaintenanceScheduleProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Scheduler\Schedule;

/**
 * @coversDefaultClass \App\Schedule\MaintenanceScheduleProvider
 */
class MaintenanceScheduleProviderTest extends TestCase
{
    /**
     * @covers ::getSchedule
     */
    public function testGetSchedule(): void
    {
        $subject = new MaintenanceScheduleProvider();
        self::assertInstanceOf(Schedule::class, $subject->getSchedule());
    }
}