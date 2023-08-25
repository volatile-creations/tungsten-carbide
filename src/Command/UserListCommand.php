<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Query\UserListResult;
use App\Message\User\ListUsers;
use App\MessageBus\QueryBusInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:user:list',
    description: 'List all users'
)]
final class UserListCommand extends Command
{
    public function __construct(
        private readonly QueryBusInterface $queryBus,
        string $name = null
    ) {
        parent::__construct($name);
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {
        /** @var UserListResult $users */
        $users = $this->queryBus->ask(new ListUsers());

        $io = new SymfonyStyle($input, $output);
        $table = $io->createTable();

        $table->setStyle('compact');
        $table->setHeaders(['UUID', 'E-mail address']);

        foreach ($users->results as $user) {
            $table->addRow(
                [
                    sprintf('<comment>%s</comment>', $user->uuid),
                    $user->emailAddress
                ]
            );
        }

        $table->render();

        return self::SUCCESS;
    }
}