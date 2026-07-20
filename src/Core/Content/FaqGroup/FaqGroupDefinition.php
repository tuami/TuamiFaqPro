<?php declare(strict_types=1);

namespace Tuami\FaqPro\Core\Content\FaqGroup;

use Tuami\FaqPro\Core\Content\Faq\FaqDefinition;
use Tuami\FaqPro\Core\Content\FaqGroup\Aggregate\FaqGroupTranslation\FaqGroupTranslationDefinition;
use Shopware\Core\Content\Rule\RuleDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\BoolField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\CascadeDelete;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\SearchRanking;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IntField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\JsonField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\LongTextField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\TranslatedField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\TranslationsAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

final class FaqGroupDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'tuami_faq_group';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return FaqGroupEntity::class;
    }

    public function getCollectionClass(): string
    {
        return FaqGroupCollection::class;
    }

    public function getDefaults(): array
    {
        return [
            'active' => true,
            'position' => 0,
            'salesChannelIds' => [],
            'productStreamIds' => [],
            'productIds' => [],
            'categoryIds' => [],
        ];
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new PrimaryKey(), new Required(), new ApiAware()),
            (new TranslatedField('name'))->addFlags(new ApiAware(), new SearchRanking(SearchRanking::HIGH_SEARCH_RANKING)),
            (new TranslatedField('description'))->addFlags(new ApiAware(), new SearchRanking(SearchRanking::LOW_SEARCH_RANKING)),
            (new BoolField('active', 'active'))->addFlags(new ApiAware()),
            (new IntField('position', 'position'))->addFlags(new ApiAware()),
            (new JsonField('sales_channel_ids', 'salesChannelIds'))->addFlags(new ApiAware()),
            (new FkField('rule_id', 'ruleId', RuleDefinition::class))->addFlags(new ApiAware()),
            (new ManyToOneAssociationField('rule', 'rule_id', RuleDefinition::class, 'id'))->addFlags(new ApiAware()),
            (new JsonField('product_stream_ids', 'productStreamIds'))->addFlags(new ApiAware()),
            (new JsonField('product_ids', 'productIds'))->addFlags(new ApiAware()),
            (new LongTextField('keywords', 'keywords'))->addFlags(new ApiAware()),
            (new JsonField('category_ids', 'categoryIds'))->addFlags(new ApiAware()),
            (new OneToManyAssociationField('faqs', FaqDefinition::class, 'group_id'))->addFlags(new ApiAware(), new CascadeDelete()),
            (new TranslationsAssociationField(FaqGroupTranslationDefinition::class, 'tuami_faq_group_id'))->addFlags(new ApiAware(), new Required()),
        ]);
    }
}