<?php
declare(strict_types=1);

namespace App\Domain\User;

use App\Domain\IdentifiesByUuid;
use EventSauce\EventSourcing\AggregateRootId;

final class UserId implements AggregateRootId
{
    use IdentifiesByUuid;
}