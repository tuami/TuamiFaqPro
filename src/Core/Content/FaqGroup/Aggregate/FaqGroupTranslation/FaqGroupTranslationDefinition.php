<?php declare(strict_types=1);

namespace Tuami\FaqPro\Core\Content\FaqGroup\Aggregate\FaqGroupTranslation;

use Tuami\FaqPro\Core\Content\FaqGroup\FaqGroupDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityTranslationDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\LongTextField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

final class FaqGroupTranslationDefinition extends EntityTranslationDefinition
{
    public const ENTITY_NAME = 'tuami_faq_group_translation';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return FaqGroupTranslationEntity::class;
    }

    public function getCollectionClass(): string
    {
        return FaqGroupTranslationCollection::class;
    }

    protected function getParentDefinitionClass(): string
    {
        return FaqGroupDefinition::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new StringField('name', 'name'))->addFlags(new ApiAware(), new Required()),
            (new LongTextField('description', 'description'))->addFlags(new ApiAware()),
        ]);
    }
}