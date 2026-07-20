<?php declare(strict_types=1);

namespace Tuami\FaqPro\Core\Content\Faq\Aggregate\FaqTranslation;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @extends EntityCollection<FaqTranslationEntity>
 */
final class FaqTranslationCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return FaqTranslationEntity::class;
    }
}