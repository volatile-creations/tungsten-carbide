<?php
declare(strict_types=1);

namespace App\MessageHandler\User;

use App\DTO\User\UserList;
use App\Message\User\GetUserList;
use App\MessageHandler\QueryHandlerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final readonly class GetUserListHandler implements QueryHandlerInterface
{
    public function __construct(
        #[Autowire('%kernel.project_dir%/var/projections/user/user_list.php')]
        private string $projectionFile
    ) {
    }

    public function __invoke(GetUserList $query): UserList
    {
        return file_exists($this->projectionFile)
            ? include $this->projectionFile
            : new UserList();
    }
}