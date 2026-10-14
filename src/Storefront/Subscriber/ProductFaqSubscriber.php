<?php declare(strict_types=1);

namespace Tuami\FaqPro\Storefront\Subscriber;

use Tuami\FaqPro\Storefront\FaqLoader;
use Tuami\FaqPro\Storefront\FaqPresentationConfig;
use Shopware\Core\Framework\Struct\ArrayStruct;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Storefront\Page\Product\ProductPageLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class ProductFaqSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly FaqLoader $faqLoader,
        private readonly SystemConfigService $systemConfigService,
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

        $items = $this->faqLoader->load($salesChannelContext, $event->getPage()->getProduct());

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
