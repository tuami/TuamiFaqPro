<?php declare(strict_types=1);

namespace Tuami\FaqPro\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

final class Migration1784325600AdvancedAssignment extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1784325600;
    }

    public function update(Connection $connection): void
    {
        $this->addColumnIfMissing($connection, 'tuami_faq_group', 'rule_id', 'BINARY(16) NULL AFTER `sales_channel_ids`');
        $this->addColumnIfMissing($connection, 'tuami_faq_group', 'product_stream_ids', 'JSON NULL AFTER `rule_id`');
        $this->addColumnIfMissing($connection, 'tuami_faq_group', 'category_ids', 'JSON NULL AFTER `product_stream_ids`');

        $foreignKeyExists = (int) $connection->fetchOne(
            'SELECT COUNT(*) FROM information_schema.table_constraints WHERE table_schema = DATABASE() AND table_name = :table AND constraint_name = :constraint',
            ['table' => 'tuami_faq_group', 'constraint' => 'fk.tuami_faq_group.rule']
        );

        if ($foreignKeyExists === 0) {
            $connection->executeStatement(<<<'SQL'
                ALTER TABLE `tuami_faq_group`
                    ADD CONSTRAINT `fk.tuami_faq_group.rule` FOREIGN KEY (`rule_id`)
                    REFERENCES `rule` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
                SQL);
        }

        $indexExists = (int) $connection->fetchOne(
            'SELECT COUNT(*) FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = :table AND index_name = :index',
            ['table' => 'tuami_faq_group', 'index' => 'idx.tuami_faq_group.rule_id']
        );

        if ($indexExists === 0) {
            $connection->executeStatement('ALTER TABLE `tuami_faq_group` ADD INDEX `idx.tuami_faq_group.rule_id` (`rule_id`)');
        }
    }

    public function updateDestructive(Connection $connection): void
    {
    }

    private function addColumnIfMissing(Connection $connection, string $table, string $column, string $definition): void
    {
        $exists = (int) $connection->fetchOne(
            'SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = :table AND column_name = :column',
            ['table' => $table, 'column' => $column]
        );

        if ($exists === 0) {
            $connection->executeStatement(\sprintf('ALTER TABLE `%s` ADD COLUMN `%s` %s', $table, $column, $definition));
        }
    }
}