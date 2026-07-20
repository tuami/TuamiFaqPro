<?php declare(strict_types=1);

namespace Tuami\FaqPro\Storefront;

use Tuami\FaqPro\Core\Content\Faq\FaqEntity;
use Tuami\FaqPro\Core\Content\FaqGroup\FaqGroupEntity;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Content\ProductStream\Service\ProductStreamBuilderInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;

final class FaqVisibilityService
{
    public function __construct(
        private readonly ProductStreamBuilderInterface $productStreamBuilder,
        private readonly EntityRepository $productRepository
    ) {
    }

    /** @param list<string> $activeRuleIds */
    public function isAvailableInSalesChannel(FaqEntity $faq, string $salesChannelId, array $activeRuleIds = []): bool
    {
        $group = $faq->getGroup();
        if (!$group instanceof FaqGroupEntity || !$group->isActive()) {
            return false;
        }

        $salesChannelIds = $group->getSalesChannelIds() ?? [];
        if ($salesChannelIds !== [] && !\in_array($salesChannelId, $salesChannelIds, true)) {
            return false;
        }

        $ruleId = $group->getRuleId();
        return $ruleId === null || \in_array($ruleId, $activeRuleIds, true);
    }

    /** @param list<string> $matchingProductStreamIds */
    public function matchesProduct(FaqEntity $faq, ProductEntity $product, array $matchingProductStreamIds = []): bool
    {
        $group = $faq->getGroup();
        if (!$group instanceof FaqGroupEntity) {
            return false;
        }

        $productIds = $group->getProductIds() ?? [];
        $categoryIds = $group->getCategoryIds() ?? [];
        $productStreamIds = $group->getProductStreamIds() ?? [];
        $keywords = $this->keywords($group->getKeywords());

        // Eine Gruppe ohne Seitenzuordnung wird bewusst nirgends automatisch eingeblendet.
        if ($productIds === [] && $categoryIds === [] && $productStreamIds === [] && $keywords === []) {
            return false;
        }

        if (\in_array($product->getId(), $productIds, true)
            || \array_intersect($product->getCategoryTree() ?? [], $categoryIds) !== []
            || \array_intersect($matchingProductStreamIds, $productStreamIds) !== []) {
            return true;
        }

        if ($keywords === []) {
            return false;
        }

        $haystack = $this->normalize(\implode(' ', \array_filter([
            $product->getName(),
            $product->getDescription(),
            $product->getProductNumber(),
        ], static fn (?string $value): bool => $value !== null && $value !== '')));

        foreach ($keywords as $keyword) {
            if (\str_contains($haystack, $keyword)) {
                return true;
            }
        }

        return false;
    }

    /** @param list<string> $categoryIds */
    public function matchesCategory(FaqEntity $faq, array $categoryIds): bool
    {
        $assignedCategoryIds = $faq->getGroup()?->getCategoryIds() ?? [];
        return $assignedCategoryIds !== [] && \array_intersect($categoryIds, $assignedCategoryIds) !== [];
    }

    /**
     * @param list<string> $productStreamIds
     * @return list<string>
     */
    public function matchingProductStreamIds(array $productStreamIds, string $productId, Context $context): array
    {
        $matches = [];
        foreach (\array_values(\array_unique($productStreamIds)) as $productStreamId) {
            try {
                $criteria = new Criteria([$productId]);
                $criteria->addFilter(...$this->productStreamBuilder->buildFilters($productStreamId, $context));
                if ($this->productRepository->searchIds($criteria, $context)->getTotal() > 0) {
                    $matches[] = $productStreamId;
                }
            } catch (\Throwable) {
                // Gelöschte oder ungültige dynamische Produktgruppen dürfen das Storefront nicht unterbrechen.
            }
        }
        return $matches;
    }

    /** @return list<string> */
    private function keywords(?string $keywords): array
    {
        if ($keywords === null || \trim($keywords) === '') {
            return [];
        }
        $parts = \preg_split('/[,;\r\n]+/u', $keywords) ?: [];
        $parts = \array_map(fn (string $keyword): string => $this->normalize($keyword), $parts);
        return \array_values(\array_unique(\array_filter($parts, static fn (string $keyword): bool => $keyword !== '')));
    }

    private function normalize(string $value): string
    {
        return \mb_strtolower(\trim(\strip_tags($value)), 'UTF-8');
    }
}