<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Locale
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 10, unique: true)]
    private string $code = '';

    #[ORM\Column(type: 'boolean')]
    private bool $active = true;

    #[ORM\OneToMany(targetEntity: BannerTranslation::class, mappedBy: 'locale')]
    private Collection $bannerTranslations;

    public function __construct()
    {
        $this->bannerTranslations = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->code;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

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

    public function getBannerTranslations(): Collection
    {
        return $this->bannerTranslations;
    }
}
