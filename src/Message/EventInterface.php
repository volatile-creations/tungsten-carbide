<?php declare(strict_types=1);

namespace App\Message;

use EventSauce\EventSourcing\Serialization\SerializablePayload;

interface EventInterface extends SerializablePayload
{
}
