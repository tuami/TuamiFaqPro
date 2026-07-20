<?php declare(strict_types=1);

namespace Tuami\FaqPro\Storefront\Subscriber;

use Tuami\FaqPro\Core\Content\Faq\FaqCollection;
use Tuami\FaqPro\Core\Content\Faq\FaqEntity;
use Tuami\FaqPro\Storefront\FaqPresentationConfig;
use Tuami\FaqPro\Storefront\FaqVisibilityService;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Shopware\Core\Framework\Struct\ArrayStruct;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Storefront\Page\Product\ProductPageLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class ProductFaqSubscriber implements EventSubscriberInterface
{
    /** @param EntityRepository<FaqCollection> $faqRepository */
    public function __construct(
        private readonly EntityRepository $faqRepository,
        private readonly SystemConfigService $systemConfigService,
        private readonly FaqVisibilityService $visibilityService,
        private readonly FaqPresentationConfig $presentationConfig
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [ProductPageLoadedEvent::class => 'onProductPageLoaded'];
    }

    public function onProductPageLoaded(ProductPageLoadedEvent $event): void
    {
        $salesChannelContext = $event->getSalesChannelContext();
        $salesChannelId = $salesChannelContext->getSalesChannelId();

        if (!$this->isEnabled('TuamiFaqPro.config.enabled', $salesChannelId)
            || !$this->isEnabled('TuamiFaqPro.config.showOnProductPages', $salesChannelId)) {
            return;
        }

        $criteria = new Criteria();
        $criteria->addAssociation('group');
        $criteria->addFilter(new EqualsFilter('active', true));
        $criteria->addFilter(new EqualsFilter('group.active', true));
        $criteria->addSorting(new FieldSorting('group.position'));
        $criteria->addSorting(new FieldSorting('position'));

        $product = $event->getPage()->getProduct();
        $faqs = $this->faqRepository->search($criteria, $event->getContext())->getEntities();
        $productStreamIds = [];

        foreach ($faqs as $faq) {
            if ($faq instanceof FaqEntity) {
                $productStreamIds = \array_merge($productStreamIds, $faq->getGroup()?->getProductStreamIds() ?? []);
            }
        }

        $matchingProductStreamIds = $this->visibilityService->matchingProductStreamIds(
            $productStreamIds,
            $product->getId(),
            $event->getContext()
        );

        $items = \array_values(\array_filter(
            $faqs->getElements(),
            fn (FaqEntity $faq): bool => $this->visibilityService->isAvailableInSalesChannel(
                $faq,
                $salesChannelId,
                $salesChannelContext->getRuleIds()
            )
                && $this->visibilityService->matchesProduct($faq, $product, $matchingProductStreamIds)
                && $faq->getQuestion() !== null
                && $faq->getAnswer() !== null
        ));

        if ($items === []) {
            return;
        }

        $event->getPage()->addExtension('tuamiFaqPro', new ArrayStruct([
            'items' => $items,
            'headline' => $this->presentationConfig->headline($salesChannelId),
            'style' => $this->presentationConfig->style($salesChannelId),
            'enableJsonLd' => $this->isEnabled('TuamiFaqPro.config.enableJsonLd', $salesChannelId),
        ], 'tuami_faq_pro'));
    }
    private function isEnabled(string $key, string $salesChannelId): bool
    {
        $value = $this->systemConfigService->get($key, $salesChannelId);

        return $value === null || (bool) $value;
    }
}