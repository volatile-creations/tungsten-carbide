<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Controller\VersionController;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * @coversDefaultClass \App\Controller\VersionController
 */
class VersionControllerTest extends TestCase
{
    /**
     * @covers ::__invoke
     */
    public function testInvoke(): void
    {
        $subject = new VersionController();
        self::assertInstanceOf(Response::class, $subject->__invoke());
    }
}