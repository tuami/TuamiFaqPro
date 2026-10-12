<?php declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Tuami\FaqPro\Core\Content\Faq\FaqDefinition;
use Tuami\FaqPro\Core\Content\Faq\Aggregate\FaqTranslation\FaqTranslationDefinition;
use Tuami\FaqPro\Core\Content\FaqGroup\FaqGroupDefinition;
use Tuami\FaqPro\Core\Content\FaqGroup\Aggregate\FaqGroupTranslation\FaqGroupTranslationDefinition;
use Tuami\FaqPro\Storefront\FaqPresentationConfig;
use Tuami\FaqPro\Storefront\FaqVisibilityService;
use Tuami\FaqPro\Storefront\Subscriber\ProductFaqSubscriber;
use Tuami\FaqPro\Storefront\Subscriber\CategoryFaqSubscriber;
use Tuami\FaqPro\Storefront\Cms\FaqCmsElementResolver;
use Tuami\FaqPro\Storefront\Controller\FaqAiController;
use Shopware\Core\Content\ProductStream\Service\ProductStreamBuilder;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();
    $services->defaults()->autowire()->autoconfigure();

    foreach ([FaqDefinition::class, FaqTranslationDefinition::class, FaqGroupDefinition::class, FaqGroupTranslationDefinition::class] as $definition) {
        $services->set($definition)->tag('shopware.entity.definition');
    }

    $services->set(FaqPresentationConfig::class);
    $services->set(FaqVisibilityService::class)
        ->arg('$productStreamBuilder', service(ProductStreamBuilder::class))
        ->arg('$productRepository', service('product.repository'));

    foreach ([ProductFaqSubscriber::class, CategoryFaqSubscriber::class] as $subscriber) {
        $services->set($subscriber)->arg('$faqRepository', service('tuami_faq.repository'));
    }

    $services->set(FaqCmsElementResolver::class)->tag('shopware.cms.data_resolver');
    $services->set(FaqAiController::class)
        ->public()
        ->arg('$faqRepository', service('tuami_faq.repository'))
        ->call('setContainer', [service('service_container')])
        ->tag('controller.service_arguments');
};
