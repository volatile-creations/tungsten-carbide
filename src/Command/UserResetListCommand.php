<?php

declare(strict_types=1);

namespace App\Command;

use App\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[AsCommand(
    name: 'app:user:reset-list',
    description: 'List URLs to password reset links for all users',
)]
final class UserResetListCommand extends Command
{
    public function __construct(
        private readonly UserRepository $repository,
        private readonly UrlGeneratorInterface $urlGenerator
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption(
            'qr',
            mode: InputOption::VALUE_NONE,
            description: 'Generate links to QR codes for the reset links'
        );

        [$console] = $GLOBALS['argv'];
        $command = $this->getName();

        $this->setHelp(
            <<<HELP
            I.e.: Run the following to download all <comment>QR codes</comment> to <comment>~/Desktop</comment>:
                <info>
                for url in `$console $command --qr`; \
                    do wget -P ~/Desktop --content-disposition "\$url"; \
                done
                </info>
            HELP

        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $route = match ($input->getOption('qr')) {
            true => 'reset_user_password_qr',
            default => 'reset_user_password'
        };

        foreach ($this->repository->findAll() as $user) {
            $output->writeln(
                $this->urlGenerator->generate(
                    name: $route,
                    parameters: ['uuid' => $user->id->toRfc4122()],
                    referenceType: UrlGeneratorInterface::ABSOLUTE_URL
                )
            );
        }

        return self::SUCCESS;
    }
}
