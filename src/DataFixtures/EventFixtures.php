<?php

namespace App\DataFixtures;

use App\Entity\Event;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\DependencyInjection\CompilerPass\FixturesCompilerPass;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag(
    FixturesCompilerPass::FIXTURE_TAG,
    ['group' => 'events']
)]
class EventFixtures extends Fixture
{
    public const array EVENTS = [
        [
            'name' => 'pre-party',
            'start' => '2024-06-07 19:00',
            'end' => '2024-06-08 12:00',
        ],
        [
            'name' => 'lunch',
            'start' => '2024-06-08 12:00',
            'end' => '2024-06-08 13:00',
        ],
        [
            'name' => 'ceremony',
            'start' => '2024-06-08 14:00',
            'end' => '2024-06-08 15:30',
        ],
        [
            'name' => 'party',
            'start' => '2024-06-08 15:30',
            'end' => '2024-06-08 23:00',
        ],
        [
            'name' => 'after-party',
            'start' => '2024-06-09 08:00',
            'end' => '2024-06-09 14:00',
        ]
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::EVENTS as ['name' => $name, 'start' => $start, 'end' => $end]) {
            $event = $manager->find(Event::class, $name) ?? new Event();
            $event->name = $name;
            $event->start = new DateTimeImmutable($start);
            $event->end = new DateTimeImmutable($end);
            $manager->persist($event);
        }

        $manager->flush();
    }
}
