<?php declare(strict_types=1);

namespace Tuami\FaqPro\Core\Content\Faq\Aggregate\FaqTranslation;

use Shopware\Core\Framework\DataAbstractionLayer\TranslationEntity;

final class FaqTranslationEntity extends TranslationEntity
{
    protected string $tuamiFaqId;

    protected ?string $question = null;

    protected ?string $answer = null;

    protected ?string $metaTitle = null;

    protected ?string $metaDescription = null;

    protected ?string $slug = null;

    public function getTuamiFaqId(): string
    {
        return $this->tuamiFaqId;
    }

    public function setTuamiFaqId(string $tuamiFaqId): void
    {
        $this->tuamiFaqId = $tuamiFaqId;
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
}