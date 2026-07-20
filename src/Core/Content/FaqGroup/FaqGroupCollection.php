<?php declare(strict_types=1);

namespace Tuami\FaqPro\Core\Content\FaqGroup;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @extends EntityCollection<FaqGroupEntity>
 */
final class FaqGroupCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return FaqGroupEntity::class;
    }
}