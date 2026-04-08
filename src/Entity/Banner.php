<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Banner
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $internalName = '';

    #[ORM\Column(type: 'string', length: 7)]
    private string $backgroundColor = '#ffffff';

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $startDate = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $endDate = null;

    #[ORM\Column(type: 'boolean')]
    private bool $active = false;

    #[ORM\OneToMany(targetEntity: BannerTranslation::class, mappedBy: 'banner', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $translations;

    public function __construct()
    {
        $this->translations = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->internalName ?: 'Banner #' . $this->id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInternalName(): string
    {
        return $this->internalName;
    }

    public function setInternalName(string $internalName): self
    {
        $this->internalName = $internalName;

        return $this;
    }

    public function getBackgroundColor(): string
    {
        return $this->backgroundColor;
    }

    public function setBackgroundColor(string $backgroundColor): self
    {
        $this->backgroundColor = $backgroundColor;

        return $this;
    }

    public function getStartDate(): ?\DateTimeImmutable
    {
        return $this->startDate;
    }

    public function setStartDate(?\DateTimeImmutable $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeImmutable
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTimeImmutable $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getTranslations(): Collection
    {
        return $this->translations;
    }

    public function addTranslation(BannerTranslation $translation): self
    {
        if (!$this->translations->contains($translation)) {
            $this->translations->add($translation);
            $translation->setBanner($this);
        }

        return $this;
    }

    public function removeTranslation(BannerTranslation $translation): self
    {
        if ($this->translations->removeElement($translation)) {
            if ($translation->getBanner() === $this) {
                $translation->setBanner(null);
            }
        }

        return $this;
    }
}