<?php declare(strict_types=1);

namespace Tuami\FaqPro\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

final class Migration1784498400GroupBasedAssignment extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1784498400;
    }

    public function update(Connection $connection): void
    {
        $this->addColumnIfMissing($connection, 'tuami_faq_group', 'product_ids', 'JSON NULL AFTER `product_stream_ids`');
        $this->addColumnIfMissing($connection, 'tuami_faq_group', 'keywords', 'LONGTEXT NULL AFTER `category_ids`');

        $groups = [];
        foreach ($connection->fetchAllAssociative('SELECT LOWER(HEX(`id`)) AS `id`, `product_ids`, `category_ids`, `keywords` FROM `tuami_faq_group`') as $row) {
            $groups[(string) $row['id']] = [
                'productIds' => $this->ids($row['product_ids'] ?? null),
                'categoryIds' => $this->ids($row['category_ids'] ?? null),
                'keywords' => $this->keywords($row['keywords'] ?? null),
            ];
        }

        $rows = $connection->fetchAllAssociative(<<<'SQL'
            SELECT LOWER(HEX(f.`group_id`)) AS `group_id`, f.`product_ids`, f.`category_ids`, ft.`keywords`
            FROM `tuami_faq` f
            LEFT JOIN `tuami_faq_translation` ft ON ft.`tuami_faq_id` = f.`id`
            SQL);

        foreach ($rows as $row) {
            $groupId = (string) $row['group_id'];
            if (!isset($groups[$groupId])) {
                continue;
            }
            $groups[$groupId]['productIds'] = $this->merge($groups[$groupId]['productIds'], $this->ids($row['product_ids'] ?? null));
            $groups[$groupId]['categoryIds'] = $this->merge($groups[$groupId]['categoryIds'], $this->ids($row['category_ids'] ?? null));
            $groups[$groupId]['keywords'] = $this->merge($groups[$groupId]['keywords'], $this->keywords($row['keywords'] ?? null));
        }

        foreach ($groups as $groupId => $assignment) {
            $connection->executeStatement(
                'UPDATE `tuami_faq_group` SET `product_ids` = :productIds, `category_ids` = :categoryIds, `keywords` = :keywords WHERE `id` = UNHEX(:id)',
                [
                    'productIds' => \json_encode($assignment['productIds'], \JSON_THROW_ON_ERROR),
                    'categoryIds' => \json_encode($assignment['categoryIds'], \JSON_THROW_ON_ERROR),
                    'keywords' => $assignment['keywords'] === [] ? null : \implode(', ', $assignment['keywords']),
                    'id' => $groupId,
                ]
            );
        }
    }

    public function updateDestructive(Connection $connection): void
    {
    }

    /** @return list<string> */
    private function ids(mixed $value): array
    {
        if (!\is_string($value) || $value === '') {
            return [];
        }
        try {
            $decoded = \json_decode($value, true, 512, \JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return [];
        }
        return \is_array($decoded) ? \array_values(\array_filter($decoded, 'is_string')) : [];
    }

    /** @return list<string> */
    private function keywords(mixed $value): array
    {
        if (!\is_string($value) || \trim($value) === '') {
            return [];
        }
        return \array_values(\array_filter(\array_map('trim', \preg_split('/[,;\r\n]+/u', $value) ?: [])));
    }

    /** @param list<string> $left @param list<string> $right @return list<string> */
    private function merge(array $left, array $right): array
    {
        return \array_values(\array_unique([...$left, ...$right]));
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