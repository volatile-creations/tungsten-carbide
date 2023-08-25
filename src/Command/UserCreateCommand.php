<?php

declare(strict_types=1);

namespace App\Command;

use App\Message\User\NextUser;
use App\MessageBus\QueryBusInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Uid\Uuid;

#[AsCommand(
    name: 'app:user:create',
    description: 'Create a new user.'
)]
final class UserCreateCommand extends Command
{
    use HandlesEmailAddress;

    private const OPTION_EMAIL = 'email';

    public function __construct(
        private readonly QueryBusInterface $queryBus,
        string $name = null
    ) {
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var Uuid $uuid */
        $uuid = $this->queryBus->ask(
            new NextUser(
                emailAddress: $input->getOption(self::OPTION_EMAIL)
            )
        );

        $io = new SymfonyStyle($input, $output);
        $io->success(sprintf('User requested: %s', $uuid));

        return self::SUCCESS;
    }
}