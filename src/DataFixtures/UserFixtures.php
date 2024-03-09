<?php

namespace App\DataFixtures;

use App\Entity\Guest;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\DependencyInjection\CompilerPass\FixturesCompilerPass;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use RuntimeException;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Uid\Factory\RandomBasedUuidFactory;

#[AutoconfigureTag(
    FixturesCompilerPass::FIXTURE_TAG,
    ['group' => 'users']
)]
final class UserFixtures extends Fixture
{
    public const string FIXTURE_PATH = __DIR__ . '/../../users.php';

    public function __construct(private readonly RandomBasedUuidFactory $uuidFactory)
    {
    }

    public function load(ObjectManager $manager): void
    {
        if (!is_readable(self::FIXTURE_PATH)) {
            return;
        }

        $users = (array)require self::FIXTURE_PATH;
        $guestNormalizer = fn (Guest $guest) => $guest->name;

        foreach ($users as $user) {
            if (!$user instanceof User) {
                throw new RuntimeException(
                    'Unexpected non-user encountered.'
                );
            }

            $subject = $manager
                ->getRepository(User::class)
                ->findOneBy(['email' => $user->email])
                ?? new User();

            $subject->id ??= $this->uuidFactory->create();
            $subject->email = $user->email;
            $subject->identifiesAs($user->self);
            $subject->self->id ??= $this->uuidFactory->create();

            $userGuests = $user->getGuests()->map($guestNormalizer)->toArray();

            foreach ($subject->getGuests() as $guest) {
                $normalized = $guestNormalizer($guest);

                if (!in_array($normalized, $userGuests)) {
                    $subject->removeGuest($guest);
                }
            }

            $subjectGuests = $subject->getGuests()->map($guestNormalizer)->toArray();

            foreach ($user->getGuests() as $guest) {
                $normalized = $guestNormalizer($guest);

                if (!in_array($normalized, $subjectGuests)) {
                    $guest->id ??= $this->uuidFactory->create();
                    $subject->addGuest($guest);
                }
            }

            $manager->persist($subject);
        }

        $manager->flush();
    }
}
