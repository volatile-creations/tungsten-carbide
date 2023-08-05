<?php

declare(strict_types=1);

namespace App\Command;

use App\Message\User\ChangeEmail;
use App\Message\User\ChangeName;
use App\MessageBus\CommandBusInterface;
use App\MessageBus\QueryBusInterface;
use LogicException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:user:update',
    description: 'Update attributes for the given user.'
)]
final class UserUpdateCommand extends Command
{
    use SelectsUser;

    private const OPTION_NAME = 'name';
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
        $this->addOption(
            name: self::OPTION_NAME,
            mode: InputOption::VALUE_REQUIRED,
            description: 'The new e-mail address'
        );
        $this->addOption(
            name: self::OPTION_EMAIL,
            mode: InputOption::VALUE_REQUIRED,
            description: 'The new e-mail address'
        );
        self::configureUserOption($this);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $user = $this->getUser($input);
        $name = $input->getOption(self::OPTION_NAME);
        $email = $input->getOption(self::OPTION_EMAIL);

        $commands = [];

        if ($name !== null && $name !== $user->name) {
            $commands[] = new ChangeName(
                uuid: $user->uuid,
                name: $name
            );
        }

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
                        ChangeName::class => 'name',
                        ChangeEmail::class => 'email',
                        default => throw new LogicException(
                            sprintf(
                                'Missing implementation for class %s',
                                get_class($command)
                            )
                        )
                    },
                    match (get_class($command)) {
                        ChangeName::class => $command->name,
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