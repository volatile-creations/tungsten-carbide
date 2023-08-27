<?php
declare(strict_types=1);

namespace App\Command\User;

use App\Message\User\DeleteUser;
use App\MessageBus\CommandBusInterface;
use App\MessageBus\QueryBusInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:user:delete',
    description: 'Delete the given user.'
)]
final class DeleteCommand extends Command
{
    use HandlesUser;

    public function __construct(
        private readonly CommandBusInterface $commandBus,
        private readonly QueryBusInterface $queryBus,
        string $name = null
    ) {
        parent::__construct($name);
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {
        $user = $this->getUser($input);

        if ($input->isInteractive()) {
            $io = new SymfonyStyle($input, $output);
            $question = sprintf(
                'Are you certain you want to delete user %s <%s>?',
                $user->id->toString(),
                $user->emailAddress
            );

            if (!$io->confirm($question, false)) {
                $io->error('Aborted deletion of user.');
                return self::FAILURE;
            }
        }

        $this->commandBus->dispatch(new DeleteUser($user->id));

        return self::SUCCESS;
    }
}