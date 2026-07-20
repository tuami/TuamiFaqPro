<?php declare(strict_types=1);

namespace Tuami\FaqPro\Storefront;

use Shopware\Core\System\SystemConfig\SystemConfigService;

final class FaqPresentationConfig
{
    public function __construct(private readonly SystemConfigService $systemConfigService)
    {
    }

    public function headline(string $salesChannelId): string
    {
        $headline = \trim((string) ($this->systemConfigService->get('TuamiFaqPro.config.headline', $salesChannelId) ?? ''));

        return $headline !== '' ? $headline : 'Häufig gestellte Fragen';
    }

    /** @return array{layoutStyle:string,widthMode:string,maxWidth:int,itemGap:int,borderRadius:int,dividerColor:string,useItemBackground:bool,itemBackgroundColor:string,activeBackgroundColor:string,activeTextColor:string,openFirstItem:bool} */
    public function style(string $salesChannelId): array
    {
        $widthMode = (string) ($this->systemConfigService->get('TuamiFaqPro.config.widthMode', $salesChannelId) ?? 'standard');
        if (!\in_array($widthMode, ['standard', 'custom', 'full'], true)) {
            $widthMode = 'standard';
        }

        $layoutStyle = (string) ($this->systemConfigService->get('TuamiFaqPro.config.layoutStyle', $salesChannelId) ?? 'cards');
        if (!\in_array($layoutStyle, ['cards', 'dividers'], true)) {
            $layoutStyle = 'cards';
        }

        $customMaxWidth = $this->integer('customMaxWidth', $salesChannelId, 1200, 320, 2400);
        $activeColorMode = (string) ($this->systemConfigService->get('TuamiFaqPro.config.activeColorMode', $salesChannelId) ?? 'primary');

        return [
            'layoutStyle' => $layoutStyle,
            'widthMode' => $widthMode,
            'maxWidth' => $widthMode === 'custom' ? $customMaxWidth : 960,
            'itemGap' => $this->integer('itemGap', $salesChannelId, 16, 0, 80),
            'borderRadius' => $this->integer('borderRadius', $salesChannelId, 8, 0, 60),
            'dividerColor' => $this->color('dividerColor', $salesChannelId, '#dee2e6'),
            'useItemBackground' => $this->boolean('useItemBackground', $salesChannelId, true),
            'itemBackgroundColor' => $this->color('itemBackgroundColor', $salesChannelId, '#f8f8f8'),
            'activeBackgroundColor' => match ($activeColorMode) {
                'secondary' => 'var(--bs-secondary, #6c757d)',
                'custom' => $this->color('activeBackgroundColor', $salesChannelId, '#0d6efd'),
                default => 'var(--bs-primary, #0d6efd)',
            },
            'activeTextColor' => $this->color('activeTextColor', $salesChannelId, '#ffffff'),
            'openFirstItem' => $this->boolean('openFirstItem', $salesChannelId, true),
        ];
    }

    private function boolean(string $name, string $salesChannelId, bool $default): bool
    {
        $value = $this->systemConfigService->get('TuamiFaqPro.config.' . $name, $salesChannelId);
        return $value === null ? $default : (bool) $value;
    }

    private function integer(string $name, string $salesChannelId, int $default, int $min, int $max): int
    {
        $value = $this->systemConfigService->get('TuamiFaqPro.config.' . $name, $salesChannelId);
        return \max($min, \min($max, \is_numeric($value) ? (int) $value : $default));
    }

    private function color(string $name, string $salesChannelId, string $default): string
    {
        $value = (string) ($this->systemConfigService->get('TuamiFaqPro.config.' . $name, $salesChannelId) ?? '');
        return \preg_match('/^#[0-9a-fA-F]{6}$/', $value) === 1 ? $value : $default;
    }
}