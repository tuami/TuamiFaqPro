<?php declare(strict_types=1);

namespace Tuami\FaqPro\Storefront\Cms;

use Tuami\FaqPro\Core\Content\Faq\FaqCollection;
use Tuami\FaqPro\Core\Content\Faq\FaqDefinition;
use Tuami\FaqPro\Core\Content\Faq\FaqEntity;
use Tuami\FaqPro\Storefront\FaqPresentationConfig;
use Tuami\FaqPro\Storefront\FaqVisibilityService;
use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\DataResolver\CriteriaCollection;
use Shopware\Core\Content\Cms\DataResolver\Element\AbstractCmsElementResolver;
use Shopware\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Shopware\Core\Framework\Struct\ArrayStruct;
use Shopware\Core\System\SystemConfig\SystemConfigService;

final class FaqCmsElementResolver extends AbstractCmsElementResolver
{
    public function __construct(
        private readonly FaqVisibilityService $visibilityService,
        private readonly SystemConfigService $systemConfigService,
        private readonly FaqPresentationConfig $presentationConfig
    ) {
    }

    public function getType(): string
    {
        return 'tuami-faq';
    }

    public function collect(CmsSlotEntity $slot, ResolverContext $resolverContext): ?CriteriaCollection
    {
        $groupId = $slot->getFieldConfig()->get('groupId')?->getStringValue();

        if ($groupId === null || $groupId === '') {
            return null;
        }

        $criteria = new Criteria();
        $criteria->addAssociation('group');
        $criteria->addFilter(new EqualsFilter('groupId', $groupId));
        $criteria->addFilter(new EqualsFilter('active', true));
        $criteria->addFilter(new EqualsFilter('group.active', true));
        $criteria->addSorting(new FieldSorting('position'));

        $collection = new CriteriaCollection();
        $collection->add($this->resultKey($slot), FaqDefinition::class, $criteria);

        return $collection;
    }

    public function enrich(CmsSlotEntity $slot, ResolverContext $resolverContext, ElementDataCollection $result): void
    {
        $salesChannelContext = $resolverContext->getSalesChannelContext();
        $salesChannelId = $salesChannelContext->getSalesChannelId();
        $headline = \trim((string) ($slot->getFieldConfig()->get('headline')?->getStringValue() ?? ''));
        if ($headline === '') {
            $headline = $this->presentationConfig->headline($salesChannelId);
        }
        $items = [];
        $searchResult = $result->get($this->resultKey($slot));

        if ($searchResult instanceof EntitySearchResult && $searchResult->getEntities() instanceof FaqCollection) {
            $items = \array_values(\array_filter(
                $searchResult->getEntities()->getElements(),
                fn (FaqEntity $faq): bool => $this->visibilityService->isAvailableInSalesChannel(
                    $faq,
                    $salesChannelId,
                    $salesChannelContext->getRuleIds()
                )
                    && $faq->getQuestion() !== null
                    && $faq->getAnswer() !== null
            ));
        }

        $slot->setData(new ArrayStruct([
            'items' => $items,
            'headline' => $headline,
            'style' => $this->presentationConfig->style($salesChannelId),
            'enableJsonLd' => $this->systemConfigService->getBool('TuamiFaqPro.config.enableJsonLd', $salesChannelId),
        ], 'tuami_faq_cms_data'));
    }

    private function resultKey(CmsSlotEntity $slot): string
    {
        return 'tuami_faq_' . $slot->getUniqueIdentifier();
    }
}