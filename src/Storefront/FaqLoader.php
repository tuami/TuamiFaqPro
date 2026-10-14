<?php declare(strict_types=1);

namespace Tuami\FaqPro\Storefront;

use Tuami\FaqPro\Core\Content\Faq\FaqCollection;
use Tuami\FaqPro\Core\Content\Faq\FaqEntity;
use Tuami\FaqPro\Core\Content\FaqGroup\FaqGroupCollection;
use Tuami\FaqPro\Core\Content\FaqGroup\FaqGroupEntity;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Framework\Adapter\Cache\CacheTagCollector;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

final class FaqLoader
{
    public const CACHE_TAG = 'tuami-faq';

    /** @param EntityRepository<FaqCollection> $faqRepository
     *  @param EntityRepository<FaqGroupCollection> $groupRepository */
    public function __construct(
        private readonly EntityRepository $faqRepository,
        private readonly EntityRepository $groupRepository,
        private readonly FaqVisibilityService $visibility,
        private readonly CacheTagCollector $cacheTags
    ) {
    }

    /** @return list<FaqEntity> */
    public function load(SalesChannelContext $context, ?ProductEntity $product = null, ?string $categoryId = null): array
    {
        // Tag even empty results: a newly assigned/created group must invalidate these pages too.
        $this->cacheTags->addTag(self::CACHE_TAG);
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('active', true));
        $criteria->addFilter(new MultiFilter(MultiFilter::CONNECTION_OR, [
            new EqualsFilter('ruleId', null),
            new EqualsAnyFilter('ruleId', $context->getRuleIds()),
        ]));
        $groups = $this->groupRepository->search($criteria, $context->getContext())->getEntities();
        $available = [];
        $streamIds = [];
        foreach ($groups as $group) {
            if (!$group instanceof FaqGroupEntity || !$this->visibility->isGroupAvailable($group, $context->getSalesChannelId(), $context->getRuleIds())) {
                continue;
            }
            $available[] = $group;
            if ($product !== null) {
                $streamIds = array_merge($streamIds, $group->getProductStreamIds() ?? []);
            }
        }
        $matchedStreams = $product === null ? [] : $this->visibility->matchingProductStreamIds($streamIds, $product->getId(), $context->getContext());
        $groupIds = [];
        foreach ($available as $group) {
            if (($product !== null && !$this->visibility->matchesGroupProduct($group, $product, $matchedStreams))
                || ($categoryId !== null && !in_array($categoryId, $group->getCategoryIds() ?? [], true))) {
                continue;
            }
            $groupIds[] = $group->getId();
        }
        if ($groupIds === []) {
            return [];
        }

        // Load translated answer bodies only for matching groups, not for the whole catalogue.
        $criteria = new Criteria();
        $criteria->addAssociation('group');
        $criteria->addFilter(new EqualsAnyFilter('groupId', $groupIds));
        $criteria->addFilter(new EqualsFilter('active', true));
        $criteria->addFilter(new EqualsFilter('group.active', true));
        $criteria->addSorting(new FieldSorting('group.position'), new FieldSorting('position'), new FieldSorting('id'));
        $faqs = $this->faqRepository->search($criteria, $context->getContext())->getEntities();
        return array_values(array_filter($faqs->getElements(), fn (FaqEntity $faq): bool =>
            $faq->getQuestion() !== null && $faq->getAnswer() !== null
            && $this->visibility->isAvailableInSalesChannel($faq, $context->getSalesChannelId(), $context->getRuleIds())
        ));
    }
}
