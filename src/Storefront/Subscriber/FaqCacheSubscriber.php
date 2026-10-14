<?php declare(strict_types=1);

namespace Tuami\FaqPro\Storefront\Subscriber;

use Shopware\Core\Framework\Adapter\Cache\CacheInvalidator;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenContainerEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Tuami\FaqPro\Storefront\FaqLoader;

final class FaqCacheSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly CacheInvalidator $invalidator) {}

    public static function getSubscribedEvents(): array
    {
        return [EntityWrittenContainerEvent::class => 'invalidate'];
    }

    public function invalidate(EntityWrittenContainerEvent $event): void
    {
        // Container events include inserts, updates and deletions (including translations).
        foreach (['tuami_faq', 'tuami_faq_translation', 'tuami_faq_group', 'tuami_faq_group_translation', 'product_stream', 'product_stream_filter', 'rule', 'rule_condition'] as $entity) {
            if ($event->getPrimaryKeys($entity) !== []) {
                $this->invalidator->invalidate([FaqLoader::CACHE_TAG], true);
                return;
            }
        }
    }
}
