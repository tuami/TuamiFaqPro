<?php declare(strict_types=1);

namespace Tuami\FaqPro\Core\Content\FaqGroup\Aggregate\FaqGroupTranslation;

use Shopware\Core\Framework\DataAbstractionLayer\TranslationEntity;

final class FaqGroupTranslationEntity extends TranslationEntity
{
    protected string $tuamiFaqGroupId;

    protected ?string $name = null;

    protected ?string $description = null;

    public function getTuamiFaqGroupId(): string
    {
        return $this->tuamiFaqGroupId;
    }

    public function setTuamiFaqGroupId(string $tuamiFaqGroupId): void
    {
        $this->tuamiFaqGroupId = $tuamiFaqGroupId;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }
}