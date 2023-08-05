<?php

declare(strict_types=1);

namespace App\Command;

use App\Message\User\CreateUser;
use App\Message\Uuid\NextIdentifier;
use App\MessageBus\CommandBusInterface;
use App\MessageBus\QueryBusInterface;
use RuntimeException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Uid\Uuid;

#[AsCommand(
    name: 'app:user:create',
    description: 'Create a new user.'
)]
final class UserCreateCommand extends Command
{
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
            description: 'The full name of the user'
        );
        $this->addOption(
            name: self::OPTION_EMAIL,
            mode: InputOption::VALUE_REQUIRED,
            description: 'The e-mail address of the user.'
        );
    }

    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        $io = new SymfonyStyle($input, $output);

        if ($input->getOption(self::OPTION_NAME) === null) {
            $input->setOption(
                self::OPTION_NAME,
                $io->ask(
                    question: 'Full name',
                    validator: static function (?string $name): string {
                        if (empty($name)) {
                            throw new RuntimeException(
                                'Please provide a name'
                            );
                        }

                        return $name;
                    }
                )
            );
        }

        if ($input->getOption(self::OPTION_EMAIL) === null) {
            $input->setOption(
                self::OPTION_EMAIL,
                $io->ask(
                    question: 'E-mail address',
                    validator: static function (?string $email): string {
                        $validated = filter_var($email, FILTER_VALIDATE_EMAIL);

                        if ($validated === false) {
                            throw new RuntimeException(
                                'Please provide a valid e-mail address'
                            );
                        }

                        return $validated;
                    }
                )
            );
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var Uuid $uuid */
        $uuid = $this->queryBus->ask(new NextIdentifier());

        $this->commandBus->dispatch(
            new CreateUser(
                uuid: $uuid,
                name: $input->getOption(self::OPTION_NAME),
                emailAddress: $input->getOption(self::OPTION_EMAIL)
            )
        );

        $io = new SymfonyStyle($input, $output);
        $io->success(sprintf('Dispatched: %s', $uuid));

        return self::SUCCESS;
    }
}