<?php
declare(strict_types=1);

namespace App\Tests\Domain\User;

use App\Domain\User\User;
use App\Domain\User\UserId;
use App\Message\User\CreateUser;
use App\Message\User\UpdateEmailAddress;
use EventSauce\EventSourcing\AggregateRootId;
use EventSauce\EventSourcing\TestUtilities\AggregateRootTestCase;
use Symfony\Component\Uid\NilUuid;

abstract class UserTestCase extends AggregateRootTestCase
{
    /** @noinspection PhpParameterNameChangedDuringInheritanceInspection */
    protected function handle(object $command): void
    {
        if ($command instanceof CreateUser) {
            $aggregate = User::create($command->userId);
            $aggregate->updateEmailAddress($command->emailAddress);
            $this->repository->persist($aggregate);
        }

        if ($command instanceof UpdateEmailAddress) {
            /** @var User $aggregate */
            $aggregate = $this->repository->retrieve($command->userId);
            $aggregate->updateEmailAddress($command->emailAddress);
            $this->repository->persist($aggregate);
        }
    }

    protected function newAggregateRootId(): AggregateRootId
    {
        return new UserId(new NilUuid());
    }

    protected function aggregateRootClassName(): string
    {
        return User::class;
    }
}
