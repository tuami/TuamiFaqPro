<?php declare(strict_types=1);

namespace Tuami\FaqPro\Storefront\Controller;

use Tuami\FaqPro\Core\Content\Faq\FaqCollection;
use Tuami\FaqPro\Core\Content\Faq\FaqEntity;
use Tuami\FaqPro\Storefront\FaqVisibilityService;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Shopware\Core\PlatformRequest;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Storefront\Controller\StorefrontController;
use Shopware\Storefront\Framework\Routing\StorefrontRouteScope;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(defaults: [PlatformRequest::ATTRIBUTE_ROUTE_SCOPE => [StorefrontRouteScope::ID]])]
final class FaqAiController extends StorefrontController
{
    /** @param EntityRepository<FaqCollection> $faqRepository */
    public function __construct(
        private readonly EntityRepository $faqRepository,
        private readonly SystemConfigService $systemConfigService,
        private readonly FaqVisibilityService $visibilityService
    ) {
    }

    #[Route(path: '/faq-ai.txt', name: 'frontend.tuami.faq.ai_feed', methods: ['GET'])]
    public function feed(SalesChannelContext $context): Response
    {
        $salesChannelId = $context->getSalesChannelId();

        if (!$this->systemConfigService->getBool('TuamiFaqPro.config.enabled', $salesChannelId)
            || !$this->systemConfigService->getBool('TuamiFaqPro.config.enableAiFeed', $salesChannelId)) {
            throw $this->createNotFoundException();
        }

        $criteria = new Criteria();
        $criteria->addAssociation('group');
        $criteria->addFilter(new EqualsFilter('active', true));
        $criteria->addFilter(new EqualsFilter('group.active', true));
        $criteria->addSorting(new FieldSorting('group.position'));
        $criteria->addSorting(new FieldSorting('position'));

        $faqs = $this->faqRepository->search($criteria, $context->getContext())->getEntities();
        $lines = ['# FAQ knowledge feed', ''];

        foreach ($faqs as $faq) {
            if (!$faq instanceof FaqEntity
                || !$this->visibilityService->isAvailableInSalesChannel($faq, $salesChannelId, $context->getRuleIds())
                || $faq->getQuestion() === null
                || $faq->getAnswer() === null) {
                continue;
            }

            $lines[] = 'Q: ' . $this->plainText($faq->getQuestion());
            $lines[] = 'A: ' . $this->plainText($faq->getAnswer());

            $lines[] = '';
        }

        $response = new Response(\implode("\n", $lines));
        $response->headers->set('Content-Type', 'text/plain; charset=UTF-8');
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        return $response;
    }

    private function plainText(string $html): string
    {
        return \trim((string) \preg_replace('/\s+/u', ' ', \html_entity_decode(\strip_tags($html), \ENT_QUOTES | \ENT_HTML5, 'UTF-8')));
    }
}