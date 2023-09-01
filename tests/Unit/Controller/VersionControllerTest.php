<?php

declare(strict_types=1);

namespace App\Tests\Unit\Controller;

use App\Controller\VersionController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(VersionController::class)]
class VersionControllerTest extends TestCase
{
    public function testInvoke(): void
    {
        $subject = new VersionController();
        self::assertInstanceOf(Response::class, $subject->__invoke());
    }
}