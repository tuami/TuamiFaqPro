<?php declare(strict_types=1);

namespace Tuami\FaqPro;

use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Doctrine\DBAL\Connection;

final class TuamiFaqPro extends Plugin
{
    public function uninstall(UninstallContext $uninstallContext): void
    {
        parent::uninstall($uninstallContext);
        if ($uninstallContext->keepUserData()) {
            return;
        }
        $connection = $this->container->get(Connection::class);
        // Only plugin-owned tables, children first. Shopware removes config and migration records.
        foreach (['tuami_faq_translation', 'tuami_faq_group_translation', 'tuami_faq', 'tuami_faq_group'] as $table) {
            $connection->executeStatement('DROP TABLE IF EXISTS `' . $table . '`');
        }
    }
}
