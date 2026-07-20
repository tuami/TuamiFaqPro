<?php declare(strict_types=1);

namespace Tuami\FaqPro\Core\Content\FaqGroup;

use Tuami\FaqPro\Core\Content\Faq\FaqCollection;
use Tuami\FaqPro\Core\Content\FaqGroup\Aggregate\FaqGroupTranslation\FaqGroupTranslationCollection;
use Shopware\Core\Content\Rule\RuleEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

final class FaqGroupEntity extends Entity
{
    use EntityIdTrait;

    protected ?string $name = null;
    protected ?string $description = null;
    protected bool $active = true;
    protected int $position = 0;
    /** @var list<string>|null */ protected ?array $salesChannelIds = null;
    protected ?string $ruleId = null;
    protected ?RuleEntity $rule = null;
    /** @var list<string>|null */ protected ?array $productStreamIds = null;
    /** @var list<string>|null */ protected ?array $productIds = null;
    /** @var list<string>|null */ protected ?array $categoryIds = null;
    protected ?string $keywords = null;
    protected ?FaqCollection $faqs = null;
    protected ?FaqGroupTranslationCollection $translations = null;

    public function getName(): ?string { return $this->name; }
    public function setName(?string $name): void { $this->name = $name; }
    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): void { $this->description = $description; }
    public function isActive(): bool { return $this->active; }
    public function getActive(): bool { return $this->active; }
    public function setActive(bool $active): void { $this->active = $active; }
    public function getPosition(): int { return $this->position; }
    public function setPosition(int $position): void { $this->position = $position; }
    /** @return list<string>|null */ public function getSalesChannelIds(): ?array { return $this->salesChannelIds; }
    /** @param list<string>|null $salesChannelIds */ public function setSalesChannelIds(?array $salesChannelIds): void { $this->salesChannelIds = $salesChannelIds; }
    public function getRuleId(): ?string { return $this->ruleId; }
    public function setRuleId(?string $ruleId): void { $this->ruleId = $ruleId; }
    public function getRule(): ?RuleEntity { return $this->rule; }
    public function setRule(?RuleEntity $rule): void { $this->rule = $rule; }
    /** @return list<string>|null */ public function getProductStreamIds(): ?array { return $this->productStreamIds; }
    /** @param list<string>|null $productStreamIds */ public function setProductStreamIds(?array $productStreamIds): void { $this->productStreamIds = $productStreamIds; }
    /** @return list<string>|null */ public function getProductIds(): ?array { return $this->productIds; }
    /** @param list<string>|null $productIds */ public function setProductIds(?array $productIds): void { $this->productIds = $productIds; }
    /** @return list<string>|null */ public function getCategoryIds(): ?array { return $this->categoryIds; }
    /** @param list<string>|null $categoryIds */ public function setCategoryIds(?array $categoryIds): void { $this->categoryIds = $categoryIds; }
    public function getKeywords(): ?string { return $this->keywords; }
    public function setKeywords(?string $keywords): void { $this->keywords = $keywords; }
    public function getFaqs(): ?FaqCollection { return $this->faqs; }
    public function setFaqs(?FaqCollection $faqs): void { $this->faqs = $faqs; }
    public function getTranslations(): ?FaqGroupTranslationCollection { return $this->translations; }
    public function setTranslations(?FaqGroupTranslationCollection $translations): void { $this->translations = $translations; }
}