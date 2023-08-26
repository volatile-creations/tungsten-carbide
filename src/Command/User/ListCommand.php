<?php
declare(strict_types=1);

namespace App\Command\User;

use App\DTO\User\UserList;
use App\Message\User\GetUserList;
use App\MessageBus\QueryBusInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:user:list',
    description: 'List available users'
)]
final class ListCommand extends Command
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
        $io = new SymfonyStyle($input, $output);
        $table = $io->createTable();
        $table->setHeaders(['ID', 'E-mail address']);
        $table->render();

        /** @var UserList $users */
        $users = $this->queryBus->ask(new GetUserList());

        foreach ($users->results as $user) {
            $table->appendRow(
                [
                    sprintf(
                        '<comment>%s</comment>',
                        $user->id->toString()
                    ),
                    $user->emailAddress
                ]
            );
        }

        return self::SUCCESS;
    }
}