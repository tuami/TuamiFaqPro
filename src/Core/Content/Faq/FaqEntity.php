<?php declare(strict_types=1);

namespace Tuami\FaqPro\Core\Content\Faq;

use Tuami\FaqPro\Core\Content\Faq\Aggregate\FaqTranslation\FaqTranslationCollection;
use Tuami\FaqPro\Core\Content\FaqGroup\FaqGroupEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

final class FaqEntity extends Entity
{
    use EntityIdTrait;

    protected string $groupId;

    protected ?string $question = null;

    protected ?string $answer = null;

    protected bool $active = true;

    protected int $position = 0;


    protected ?string $metaTitle = null;

    protected ?string $metaDescription = null;

    protected ?string $slug = null;

    protected bool $noIndex = false;

    protected ?FaqGroupEntity $group = null;

    protected ?FaqTranslationCollection $translations = null;

    public function getGroupId(): string
    {
        return $this->groupId;
    }

    public function setGroupId(string $groupId): void
    {
        $this->groupId = $groupId;
    }

    public function getQuestion(): ?string
    {
        return $this->question;
    }

    public function setQuestion(?string $question): void
    {
        $this->question = $question;
    }

    public function getAnswer(): ?string
    {
        return $this->answer;
    }

    public function setAnswer(?string $answer): void
    {
        $this->answer = $answer;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function getActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): void
    {
        $this->position = $position;
    }


    public function getMetaTitle(): ?string
    {
        return $this->metaTitle;
    }

    public function setMetaTitle(?string $metaTitle): void
    {
        $this->metaTitle = $metaTitle;
    }

    public function getMetaDescription(): ?string
    {
        return $this->metaDescription;
    }

    public function setMetaDescription(?string $metaDescription): void
    {
        $this->metaDescription = $metaDescription;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): void
    {
        $this->slug = $slug;
    }

    public function isNoIndex(): bool
    {
        return $this->noIndex;
    }

    public function getNoIndex(): bool
    {
        return $this->noIndex;
    }

    public function setNoIndex(bool $noIndex): void
    {
        $this->noIndex = $noIndex;
    }

    public function getGroup(): ?FaqGroupEntity
    {
        return $this->group;
    }

    public function setGroup(?FaqGroupEntity $group): void
    {
        $this->group = $group;
    }

    public function getTranslations(): ?FaqTranslationCollection
    {
        return $this->translations;
    }

    public function setTranslations(?FaqTranslationCollection $translations): void
    {
        $this->translations = $translations;
    }
}