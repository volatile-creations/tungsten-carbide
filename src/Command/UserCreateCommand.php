<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Override;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Uid\Factory\RandomBasedUuidFactory;

#[AsCommand(
    name: 'app:user:create',
    description: 'Create a new user',
)]
final class UserCreateCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly RandomBasedUuidFactory $uuidFactory
    ) {
        parent::__construct();
    }

    #[Override]
    protected function configure(): void
    {
        $this->addArgument(
            name: 'name',
            mode: InputArgument::REQUIRED,
            description: 'The display name of the user.'
        );
        $this->addOption(
            name: 'email',
            mode: InputOption::VALUE_REQUIRED,
            description: 'The email address'
        );
    }

    #[Override]
    protected function interact(
        InputInterface $input,
        OutputInterface $output
    ): void {
        $email = $input->getOption('email');

        if (empty($email)) {
            $io = new SymfonyStyle($input, $output);
            $input->setOption(
                'email',
                $io->ask(
                    question: 'What is your e-mail address?',
                    validator: static function (?string $value): string {
                        $value = trim($value ?? '');

                        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            throw new \InvalidArgumentException(
                                sprintf(
                                    '"%s" is not a valid email address.',
                                    $value
                                )
                            );
                        }

                        return $value;
                    }
                )
            );
        }
    }

    #[Override]
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {
        $user = new User();
        $user->id = $this->uuidFactory->create();
        $user->name = trim($input->getArgument('name'));
        $user->email = trim($input->getOption('email'));

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io = new SymfonyStyle($input, $output);
        $io->success(
            sprintf(
                'Created user (%s) %s <%s>',
                $user->id->toRfc4122(),
                $user->name,
                $user->email
            )
        );

        return self::SUCCESS;
    }
}
