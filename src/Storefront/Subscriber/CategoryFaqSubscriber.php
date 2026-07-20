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
use Shopware\Storefront\Page\Navigation\NavigationPageLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class CategoryFaqSubscriber implements EventSubscriberInterface
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
        return [NavigationPageLoadedEvent::class => 'onNavigationPageLoaded'];
    }

    public function onNavigationPageLoaded(NavigationPageLoadedEvent $event): void
    {
        $category = $event->getPage()->getCategory();
        $salesChannelContext = $event->getSalesChannelContext();
        $salesChannelId = $salesChannelContext->getSalesChannelId();

        if ($category === null
            || !$this->isEnabled('TuamiFaqPro.config.enabled', $salesChannelId)
            || !$this->isEnabled('TuamiFaqPro.config.showOnCategoryPages', $salesChannelId)) {
            return;
        }

        $criteria = new Criteria();
        $criteria->addAssociation('group');
        $criteria->addFilter(new EqualsFilter('active', true));
        $criteria->addFilter(new EqualsFilter('group.active', true));
        $criteria->addSorting(new FieldSorting('group.position'));
        $criteria->addSorting(new FieldSorting('position'));

        $faqs = $this->faqRepository->search($criteria, $event->getContext())->getEntities();
        $items = \array_values(\array_filter(
            $faqs->getElements(),
            fn (FaqEntity $faq): bool => $this->visibilityService->isAvailableInSalesChannel(
                $faq,
                $salesChannelId,
                $salesChannelContext->getRuleIds()
            )
                && $this->visibilityService->matchesCategory($faq, $this->categoryIds($category->getId(), $category->getPath()))
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

    /** @return list<string> */
    private function categoryIds(string $categoryId, ?string $path): array
    {
        $parentIds = $path === null ? [] : \array_filter(\explode('|', \trim($path, '|')));

        return \array_values(\array_unique([...$parentIds, $categoryId]));
    }
}