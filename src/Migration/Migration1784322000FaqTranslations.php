<?php declare(strict_types=1);

namespace Tuami\FaqPro\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Migration\MigrationStep;
use Shopware\Core\Framework\Uuid\Uuid;

final class Migration1784322000FaqTranslations extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1784322000;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement(<<<'SQL'
            CREATE TABLE IF NOT EXISTS `tuami_faq_group_translation` (
                `tuami_faq_group_id` BINARY(16) NOT NULL,
                `language_id` BINARY(16) NOT NULL,
                `name` VARCHAR(255) NOT NULL,
                `description` LONGTEXT NULL,
                `created_at` DATETIME(3) NOT NULL,
                `updated_at` DATETIME(3) NULL,
                PRIMARY KEY (`tuami_faq_group_id`, `language_id`),
                CONSTRAINT `fk.tuami_faq_group_translation.group` FOREIGN KEY (`tuami_faq_group_id`)
                    REFERENCES `tuami_faq_group` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT `fk.tuami_faq_group_translation.language` FOREIGN KEY (`language_id`)
                    REFERENCES `language` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            SQL);

        $connection->executeStatement(<<<'SQL'
            CREATE TABLE IF NOT EXISTS `tuami_faq_translation` (
                `tuami_faq_id` BINARY(16) NOT NULL,
                `language_id` BINARY(16) NOT NULL,
                `question` VARCHAR(500) NOT NULL,
                `answer` LONGTEXT NOT NULL,
                `keywords` LONGTEXT NULL,
                `meta_title` VARCHAR(255) NULL,
                `meta_description` VARCHAR(500) NULL,
                `slug` VARCHAR(255) NULL,
                `created_at` DATETIME(3) NOT NULL,
                `updated_at` DATETIME(3) NULL,
                PRIMARY KEY (`tuami_faq_id`, `language_id`),
                KEY `idx.tuami_faq_translation.slug` (`language_id`, `slug`),
                CONSTRAINT `fk.tuami_faq_translation.faq` FOREIGN KEY (`tuami_faq_id`)
                    REFERENCES `tuami_faq` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT `fk.tuami_faq_translation.language` FOREIGN KEY (`language_id`)
                    REFERENCES `language` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            SQL);

        $languageId = Uuid::fromHexToBytes(Defaults::LANGUAGE_SYSTEM);

        $connection->executeStatement(<<<'SQL'
            INSERT IGNORE INTO `tuami_faq_group_translation`
                (`tuami_faq_group_id`, `language_id`, `name`, `description`, `created_at`, `updated_at`)
            SELECT `id`, :languageId, COALESCE(NULLIF(`name`, ''), 'FAQ-Gruppe'), `description`, `created_at`, `updated_at`
            FROM `tuami_faq_group`
            SQL, ['languageId' => $languageId]);

        $connection->executeStatement(<<<'SQL'
            INSERT IGNORE INTO `tuami_faq_translation`
                (`tuami_faq_id`, `language_id`, `question`, `answer`, `keywords`, `meta_title`, `meta_description`, `slug`, `created_at`, `updated_at`)
            SELECT `id`, :languageId, COALESCE(NULLIF(`question`, ''), 'FAQ'), COALESCE(`answer`, ''), `keywords`, `meta_title`, `meta_description`, `slug`, `created_at`, `updated_at`
            FROM `tuami_faq`
            SQL, ['languageId' => $languageId]);

        $connection->executeStatement(<<<'SQL'
            ALTER TABLE `tuami_faq_group`
                MODIFY `name` VARCHAR(255) NULL,
                MODIFY `description` LONGTEXT NULL
            SQL);

        $connection->executeStatement(<<<'SQL'
            ALTER TABLE `tuami_faq`
                MODIFY `question` VARCHAR(500) NULL,
                MODIFY `answer` LONGTEXT NULL
            SQL);

        $this->addIndexIfMissing($connection, 'tuami_faq', 'idx.tuami_faq.group_id', '`group_id`');
        $this->addIndexIfMissing($connection, 'tuami_faq', 'idx.tuami_faq.active_position', '`active`, `position`');
        $this->addIndexIfMissing($connection, 'tuami_faq_group', 'idx.tuami_faq_group.active_position', '`active`, `position`');
    }

    public function updateDestructive(Connection $connection): void
    {
    }

    private function addIndexIfMissing(Connection $connection, string $table, string $index, string $columns): void
    {
        $exists = $connection->fetchOne(
            'SELECT COUNT(*) FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = :table AND index_name = :index',
            ['table' => $table, 'index' => $index]
        );

        if ((int) $exists === 0) {
            $connection->executeStatement(\sprintf('ALTER TABLE `%s` ADD INDEX `%s` (%s)', $table, $index, $columns));
        }
    }
}