<?php declare(strict_types=1);

namespace Tuami\FaqPro\Core\Content\Faq\Aggregate\FaqTranslation;

use Tuami\FaqPro\Core\Content\Faq\FaqDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityTranslationDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\LongTextField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

final class FaqTranslationDefinition extends EntityTranslationDefinition
{
    public const ENTITY_NAME = 'tuami_faq_translation';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return FaqTranslationEntity::class;
    }

    public function getCollectionClass(): string
    {
        return FaqTranslationCollection::class;
    }

    protected function getParentDefinitionClass(): string
    {
        return FaqDefinition::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new StringField('question', 'question', 500))->addFlags(new ApiAware(), new Required()),
            (new LongTextField('answer', 'answer'))->addFlags(new ApiAware(), new Required()),
            (new StringField('meta_title', 'metaTitle'))->addFlags(new ApiAware()),
            (new StringField('meta_description', 'metaDescription', 500))->addFlags(new ApiAware()),
            (new StringField('slug', 'slug'))->addFlags(new ApiAware()),
        ]);
    }
}