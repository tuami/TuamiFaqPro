<?php declare(strict_types=1);

namespace Tuami\FaqPro\Core\Content\FaqGroup\Aggregate\FaqGroupTranslation;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @extends EntityCollection<FaqGroupTranslationEntity>
 */
final class FaqGroupTranslationCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return FaqGroupTranslationEntity::class;
    }
}