<?php

declare(strict_types=1);

namespace App\Command;

use App\Message\User\ChangeEmail;
use App\MessageBus\CommandBusInterface;
use App\MessageBus\QueryBusInterface;
use LogicException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:user:update',
    description: 'Update attributes for the given user.'
)]
final class UserUpdateCommand extends Command
{
    use HandlesUser, HandlesEmailAddress {
        HandlesUser::configure as protected configureUserOption;
        HandlesUser::interact as protected interactUser;
        HandlesEmailAddress::configure as protected configureEmailOption;
        HandlesEmailAddress::interact as protected interactEmail;
    }

    private const OPTION_EMAIL = 'email';

    public function __construct(
        private readonly CommandBusInterface $commandBus,
        private readonly QueryBusInterface $queryBus,
        string $name = null
    ) {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this->configureEmailOption();
        $this->configureUserOption();
    }

    protected function interact(
        InputInterface $input,
        OutputInterface $output
    ): void {
        $this->interactUser($input, $output);
        $this->interactEmail($input, $output);
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {
        $user = $this->getUser($input);
        $email = $this->getEmail($input);

        $commands = [];

        if ($email !== null && $email !== $user->emailAddress) {
            $commands[] = new ChangeEmail(
                uuid: $user->uuid,
                emailAddress: $email
            );
        }

        $io = new SymfonyStyle($input, $output);

        if (count($commands) === 0) {
            $io->error(
                sprintf('No changes found for user %s', $user->uuid)
            );
            return self::FAILURE;
        }

        foreach ($commands as $command) {
            $this->commandBus->dispatch($command);
            $io->success(
                sprintf(
                    'Dispatch: change %s to "%s" for user %s',
                    match (get_class($command)) {
                        ChangeEmail::class => 'email',
                        default => throw new LogicException(
                            sprintf(
                                'Missing implementation for class %s',
                                get_class($command)
                            )
                        )
                    },
                    match (get_class($command)) {
                        ChangeEmail::class => $command->emailAddress,
                        default => 'Unknown'
                    },
                    $user->uuid
                )
            );
        }

        return self::SUCCESS;
    }

}