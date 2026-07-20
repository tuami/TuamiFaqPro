<?php declare(strict_types=1);

namespace Tuami\FaqPro\Core\Content\Faq;

use Tuami\FaqPro\Core\Content\Faq\Aggregate\FaqTranslation\FaqTranslationDefinition;
use Tuami\FaqPro\Core\Content\FaqGroup\FaqGroupDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\BoolField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\SearchRanking;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IntField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\TranslatedField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\TranslationsAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

final class FaqDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'tuami_faq';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return FaqEntity::class;
    }

    public function getCollectionClass(): string
    {
        return FaqCollection::class;
    }

    public function getDefaults(): array
    {
        return [
            'active' => true,
            'position' => 0,
            'noIndex' => false,
        ];
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new PrimaryKey(), new Required(), new ApiAware()),
            (new FkField('group_id', 'groupId', FaqGroupDefinition::class))->addFlags(new Required(), new ApiAware()),
            (new TranslatedField('question'))->addFlags(new ApiAware(), new SearchRanking(SearchRanking::HIGH_SEARCH_RANKING)),
            (new TranslatedField('answer'))->addFlags(new ApiAware(), new SearchRanking(SearchRanking::MIDDLE_SEARCH_RANKING)),
            (new BoolField('active', 'active'))->addFlags(new ApiAware()),
            (new IntField('position', 'position'))->addFlags(new ApiAware()),
            (new TranslatedField('metaTitle'))->addFlags(new ApiAware()),
            (new TranslatedField('metaDescription'))->addFlags(new ApiAware()),
            (new TranslatedField('slug'))->addFlags(new ApiAware()),
            (new BoolField('no_index', 'noIndex'))->addFlags(new ApiAware()),
            (new ManyToOneAssociationField('group', 'group_id', FaqGroupDefinition::class, 'id'))->addFlags(new ApiAware()),
            (new TranslationsAssociationField(FaqTranslationDefinition::class, 'tuami_faq_id'))->addFlags(new ApiAware(), new Required()),
        ]);
    }
}