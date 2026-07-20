<?php declare(strict_types=1);

namespace Tuami\FaqPro\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

final class Migration1720000000Faq extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1720000000;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement(<<<'SQL'
            CREATE TABLE IF NOT EXISTS `tuami_faq_group` (
                `id` BINARY(16) NOT NULL,
                `name` VARCHAR(255) NOT NULL,
                `description` LONGTEXT NULL,
                `active` TINYINT(1) NOT NULL DEFAULT 1,
                `position` INT NOT NULL DEFAULT 0,
                `sales_channel_ids` JSON NULL,
                `created_at` DATETIME(3) NOT NULL,
                `updated_at` DATETIME(3) NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            SQL);

        $connection->executeStatement(<<<'SQL'
            CREATE TABLE IF NOT EXISTS `tuami_faq` (
                `id` BINARY(16) NOT NULL,
                `group_id` BINARY(16) NOT NULL,
                `question` VARCHAR(500) NOT NULL,
                `answer` LONGTEXT NOT NULL,
                `active` TINYINT(1) NOT NULL DEFAULT 1,
                `position` INT NOT NULL DEFAULT 0,
                `keywords` LONGTEXT NULL,
                `product_ids` JSON NULL,
                `category_ids` JSON NULL,
                `meta_title` VARCHAR(255) NULL,
                `meta_description` VARCHAR(500) NULL,
                `slug` VARCHAR(255) NULL,
                `no_index` TINYINT(1) NOT NULL DEFAULT 0,
                `created_at` DATETIME(3) NOT NULL,
                `updated_at` DATETIME(3) NULL,
                PRIMARY KEY (`id`),
                CONSTRAINT `fk.tuami_faq.group` FOREIGN KEY (`group_id`)
                    REFERENCES `tuami_faq_group` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            SQL);
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}