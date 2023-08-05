<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\User;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[CoversClass(User::class)]
#[UsesClass(Uuid::class)]
final class UserTest extends KernelTestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        $this->validator = self::getContainer()->get(ValidatorInterface::class);
    }

    #[DataProvider('userProvider')]
    public function testUser(Uuid $uuid, string $emailAddress, string $name, array $expectedViolations = []): void
    {
        $user = new User();
        $user->setUuid($uuid);
        $user->setEmailAddress($emailAddress);
        $user->setName($name);

        $violations = $this->validator->validate($user);

        $this->assertCount(count($expectedViolations), $violations);

        for ($idx = 0; $idx < count($expectedViolations); $idx++) {
            $this->assertEquals(
                $expectedViolations[$idx],
                $violations->get($idx)->getMessage()
            );
        }

        // Test getters
        $this->assertTrue($uuid->equals($user->getUuid()));
        $this->assertEquals($emailAddress, $user->getEmailAddress());
        $this->assertEquals($name, $user->getName());
    }

    public static function userProvider(): array
    {
        return [
            // Valid users.
            [Uuid::v7(), 'test@example.com', 'John Doe'],
            [Uuid::v7(), 'another@example.com', 'Jane Smith'],

            // Invalid users.
            'Invalid email address' => [
                Uuid::v7(),
                'invalid_email',
                'John Doe',
                ['This value is not a valid email address.']
            ],
            'Invalid name: too short' => [
                Uuid::v7(),
                'test@example.com',
                'J',
                ['Your name must be at least 2 characters long']
            ],
            'Invalid name: too long' => [
                Uuid::v7(),
                'test@example.com',
                str_repeat('a', 51),
                ['Your name cannot be longer than 50 characters']
            ]
        ];
    }
}