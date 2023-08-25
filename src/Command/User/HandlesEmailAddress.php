<?php
declare(strict_types=1);

namespace App\Command\User;

use RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

trait HandlesEmailAddress
{
    private static string $optionEmailAddress = 'email';

    protected function configure(): void
    {
        $this->addOption(
            name: self::$optionEmailAddress,
            mode: InputOption::VALUE_REQUIRED,
            description: 'The user email address'
        );
    }

    protected function interact(
        InputInterface $input,
        OutputInterface $output
    ): void {
        if ($input->getOption(self::$optionEmailAddress) === null) {
            $io = new SymfonyStyle($input, $output);

            $input->setOption(
                self::$optionEmailAddress,
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

    protected function getEmail(InputInterface $input): ?string
    {
        return $input->getOption(self::$optionEmailAddress);
    }
}