<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Migration\MessageStorageMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230825170811 extends MessageStorageMigration
{
    public function getTableName(): string
    {
        return 'user_events';
    }
}
