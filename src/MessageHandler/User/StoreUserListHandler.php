<?php
declare(strict_types=1);

namespace App\MessageHandler\User;

use App\Domain\User\UserId;
use App\DTO\User\User;
use App\DTO\User\UserList;
use App\Message\User\StoreUserList;
use App\MessageHandler\CommandHandlerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Filesystem\Filesystem;

final readonly class StoreUserListHandler implements CommandHandlerInterface
{
    public function __construct(
        #[Autowire('%kernel.project_dir%/var/projections/user/user_list.php')]
        private string $projectionFile,
        private Filesystem $filesystem
    ) {
    }

    public function __invoke(StoreUserList $command): void
    {
        $this->filesystem->dumpFile(
            $this->projectionFile,
            self::formatUserList($command->userList)
        );
    }

    private static function formatUserList(UserList $userList): string
    {
        $result = <<<PHP
        <?php
        declare(strict_types=1);
        
        namespace App\Projection;

        PHP;

        $result .= sprintf('use %s;', User::class);
        $result .= sprintf('use %s;', UserId::class);
        $result .= sprintf('use %s;', UserList::class);

        $result .= <<<PHP
        
        return new UserList(
        PHP;

        foreach ($userList->results as $user) {
            $result .= sprintf(
                <<<PHP
                    new User(
                        id: UserId::fromString('%s'),
                        emailAddress: '%s'
                    ),
                PHP,
                $user->id->toString(),
                $user->emailAddress
            );
        }

        return $result . ');';
    }
}