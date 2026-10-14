<?php declare(strict_types=1);

// Run with a temporary Symfony 7.4 / Twig 3 vendor autoloader, not a Shopware integration test.
require $argv[2];
$root = $argv[1];
$container = new Symfony\Component\DependencyInjection\ContainerBuilder();
$loader = new Symfony\Component\DependencyInjection\Loader\PhpFileLoader(
    $container,
    new Symfony\Component\Config\FileLocator($root . '/src/Resources/config')
);
$loader->load('services.php');
$definitions = array_filter($container->getDefinitions(), static fn(string $id): bool => str_starts_with($id, 'Tuami\\FaqPro\\'), ARRAY_FILTER_USE_KEY);
if (count($definitions) !== 12) {
    throw new RuntimeException('Expected exactly 12 plugin services');
}
$visibility = $container->getDefinition('Tuami\FaqPro\Storefront\FaqVisibilityService');
if ((string) $visibility->getArgument('$productRepository') !== 'product.repository') {
    throw new RuntimeException('Product repository wiring changed');
}
foreach ($definitions as $id => $definition) {
    if (!$definition->isAutowired() || !$definition->isAutoconfigured()) {
        throw new RuntimeException('Missing defaults: ' . $id);
    }
}
if (!$container->getDefinition('Tuami\FaqPro\Storefront\Controller\FaqAiController')->isPublic()) {
    throw new RuntimeException('AI controller must remain public');
}
echo "PASS Symfony PHP service configuration: 12 services, defaults and wiring\n";

// Resolve the route import with Symfony; capture its path/type without booting Shopware.
$capture = new class extends Symfony\Component\Config\Loader\Loader {
    public array $imports = [];
    public function supports(mixed $resource, ?string $type = null): bool { return $type === 'attribute'; }
    public function load(mixed $resource, ?string $type = null): mixed {
        $this->imports[] = [$resource, $type];
        return new Symfony\Component\Routing\RouteCollection();
    }
};
$routingLoader = new Symfony\Component\Routing\Loader\PhpFileLoader(new Symfony\Component\Config\FileLocator($root . '/src/Resources/config'));
$resolver = new Symfony\Component\Config\Loader\LoaderResolver([$routingLoader, $capture]);
$routingLoader->load('routes.php');
if (count($capture->imports) !== 1 || $capture->imports[0][1] !== 'attribute'
    || !str_ends_with(str_replace('\\', '/', $capture->imports[0][0]), 'Storefront/Controller/')) {
    throw new RuntimeException('Controller route import changed');
}
echo "PASS Symfony PHP route configuration: controller attribute import\n";

$twig = new Twig\Environment(new Twig\Loader\FilesystemLoader($root . '/src/Resources/views'), ['strict_variables' => true, 'autoescape' => 'html']);
$twig->addFunction(new Twig\TwigFunction('asset', static fn(string $path): string => '/' . $path));
$twig->addFilter(new Twig\TwigFilter('sw_sanitize', 'strip_tags'));
$style = ['layoutStyle' => 'cards', 'widthMode' => 'default', 'useItemBackground' => true, 'maxWidth' => 960,
    'itemGap' => 12, 'borderRadius' => 8, 'dividerColor' => '#cccccc', 'itemBackgroundColor' => '#ffffff',
    'activeBackgroundColor' => '#000000', 'activeTextColor' => '#ffffff', 'openFirstItem' => false];
$html = $twig->render('storefront/component/tuami-faq-list.html.twig', ['faqData' => [
    'headline' => 'FAQ', 'style' => $style, 'enableJsonLd' => true,
    'items' => [['question' => 'Größe </script><script>alert(1)</script>', 'answer' => 'Antwort & Details']],
]]);
if (!preg_match('~<script type="application/ld\+json">(.*?)</script>~s', $html, $match)) {
    throw new RuntimeException('Missing structured FAQ data');
}
$data = json_decode($match[1], true, 512, JSON_THROW_ON_ERROR);
if ($data['mainEntity'][0]['acceptedAnswer']['text'] !== 'Antwort & Details' || str_contains($html, '<script>alert(1)</script>')) {
    throw new RuntimeException('Structured FAQ output changed or unsafe script boundary');
}
echo "PASS Twig component renders in isolated context and JSON-LD round-trips safely\n";
foreach (['center', 'left', 'hidden'] as $display) {
    $style['headlineDisplay'] = $display;
    $html = $twig->render('storefront/component/tuami-faq-list.html.twig', ['faqData' => [
        'headline' => 'FAQ', 'style' => $style, 'enableJsonLd' => false,
        'items' => [['question' => 'Frage', 'answer' => 'Antwort']],
    ]]);
    if ((str_contains($html, '<h2 ') !== ($display !== 'hidden'))
        || (str_contains($html, 'tuami-faq-pro__title--left') !== ($display === 'left'))
        || ($display === 'hidden' && (!str_contains($html, 'aria-label="FAQ"') || str_contains($html, 'aria-labelledby=')))) {
        throw new RuntimeException('Heading rendering failed: ' . $display);
    }
}
echo "PASS heading modes: centered, left-aligned, hidden without orphan ARIA reference\n";
