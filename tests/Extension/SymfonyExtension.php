<?php /** @noinspection PhpUnused */

declare(strict_types=1);

namespace App\Tests\Extension;

use App\Tests\Event\TestRunner\SetupApplication;
use PHPUnit\Runner\Extension\Extension;
use PHPUnit\Runner\Extension\Facade;
use PHPUnit\Runner\Extension\ParameterCollection;
use PHPUnit\TextUI\Configuration\Configuration;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\NullOutput;

final readonly class SymfonyExtension implements Extension
{
    public function bootstrap(
        Configuration $configuration,
        Facade $facade,
        ParameterCollection $parameters
    ): void {
        $output = self::isEnabled($parameters, 'output')
            ? new ConsoleOutput()
            : new NullOutput();

        $facade->registerSubscriber(
            new SetupApplication(
                output: $output,
                clearCache: self::isEnabled($parameters, 'clearCache'),
                migrateDatabase: self::isEnabled($parameters, 'migrateDatabase'),
                loadFixtures: self::isEnabled($parameters, 'loadFixtures')
            )
        );
    }

    private static function isEnabled(
        ParameterCollection $parameters,
        string $key
    ): bool {
        return strtolower($parameters->get($key)) === 'true';
    }
}