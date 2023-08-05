<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Query\UserListResult;
use App\Entity\Query\UserResult;
use App\Message\User\GetUser;
use App\Message\User\ListUsers;
use App\MessageBus\QueryBusInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Uid\Uuid;

trait SelectsUser
{
    private readonly QueryBusInterface $queryBus;
    private static string $optionUser = 'user';

    protected static function configureUserOption(Command $command): void
    {
        $command->addOption(
            name: self::$optionUser,
            mode: InputOption::VALUE_REQUIRED,
            description: 'The user UUID'
        );
    }

    protected function interact(
        InputInterface $input,
        OutputInterface $output
    ): void {
        $io = new SymfonyStyle($input, $output);

        if ($input->getOption(self::$optionUser) === null) {
            /** @var UserListResult $userList */
            $userList = $this->queryBus->ask(new ListUsers());

            $input->setOption(
                self::$optionUser,
                $io->askQuestion(
                    new ChoiceQuestion(
                        question: 'Select a user',
                        choices: array_reduce(
                            $userList->results,
                            static fn (array $carry, UserResult $result) => [
                                ...$carry,
                                (string)$result->uuid => $result->name
                            ],
                            []
                        )
                    )
                )
            );
        }
    }

    private static function getUserUuid(InputInterface $input): Uuid
    {
        return Uuid::fromString($input->getOption(self::$optionUser));
    }

    private function getUser(InputInterface $input): UserResult
    {
        return $this->queryBus->ask(
            new GetUser(uuid: self::getUserUuid($input))
        );
    }
}