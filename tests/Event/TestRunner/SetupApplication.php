<?php

declare(strict_types=1);

namespace App\Tests\Event\TestRunner;

use PHPUnit\Event\TestRunner\Started;
use PHPUnit\Event\TestRunner\StartedSubscriber;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

final readonly class SetupApplication implements StartedSubscriber
{
    public function __construct(
        private OutputInterface $output,
        private bool $clearCache,
        private bool $migrateDatabase,
        private bool $loadFixtures
    ) {
    }

    public function notify(Started $event): void
    {
        $this->output->write('Setting up Symfony application. . .');

        if ($this->clearCache) {
            self::run('cache:pool:clear', 'cache.global_clearer');
            self::run('cache:clear');

            if ($this->migrateDatabase) {
                self::run('doctrine:cache:clear-metadata', '--no-debug', '--flush');
            }
        }

        if ($this->migrateDatabase) {
            try {
                self::run('doctrine:database:drop', '--force');
            } catch (ProcessFailedException) {
                // Is allowed to fail.
            }

            self::run('doctrine:database:create');
            self::run('doctrine:migrations:migrate');
        }

        if ($this->loadFixtures) {
            self::run('doctrine:fixtures:load');
        }

        $this->output->writeln(' [OK]');
    }

    private static function run(string ...$arguments): void
    {
        if (!in_array('--no-interaction', $arguments, true)
            || !in_array('-n', $arguments, true)
        ) {
            $arguments[] = '--no-interaction';
        }

        $process = new Process(command: ['bin/console', ...$arguments]);
        $process->setWorkingDirectory(
            realpath(__DIR__ . '/../../../')
        );
        $process->setEnv(
            [
                'APP_ENV' => 'test',
                ...$process->getEnv()
            ]
        );
        $process->mustRun();
    }
}