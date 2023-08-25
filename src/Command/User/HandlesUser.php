<?php

declare(strict_types=1);

namespace App\Command\User;

use App\DTO\User\User;
use App\DTO\User\UserList;
use App\Message\User\GetUser;
use App\MessageBus\QueryBusInterface;
use RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

trait HandlesUser
{
    private readonly QueryBusInterface $queryBus;
    private static string $optionUser = 'user';

    protected function configure(): void
    {
        $this->addOption(
            name: self::$optionUser,
            mode: InputOption::VALUE_REQUIRED,
            description: 'The user UUID'
        );
    }

    protected function interact(
        InputInterface $input,
        OutputInterface $output
    ): void {
        if ($input->getOption(self::$optionUser) === null) {
            throw new RuntimeException('Missing implementation to list users.');

            /** @var UserList $userList */
            $userList = $this->queryBus->ask(new ListUsers());
            $io = new SymfonyStyle($input, $output);

            $input->setOption(
                self::$optionUser,
                $io->askQuestion(
                    new ChoiceQuestion(
                        question: 'Select a user',
                        choices: array_reduce(
                            $userList->results,
                            static fn (array $carry, User $result) => [
                                ...$carry,
                                $result->id->toString() => $result->emailAddress
                            ],
                            []
                        )
                    )
                )
            );
        }
    }

    private function getUser(InputInterface $input): User
    {
        return $this->queryBus->ask(
            new GetUser(userId: $input->getOption(self::$optionUser))
        );
    }
}