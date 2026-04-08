<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class BannerTranslation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'text')]
    private string $content = '';

    #[ORM\ManyToOne(targetEntity: Locale::class, inversedBy: 'bannerTranslations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Locale $locale = null;

    #[ORM\ManyToOne(targetEntity: Banner::class, inversedBy: 'translations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Banner $banner = null;

    public function __toString(): string
    {
        return '[' . ($this->locale?->getCode() ?? '?') . '] ' . mb_substr($this->content, 0, 40);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLocale(): ?Locale
    {
        return $this->locale;
    }

    public function setLocale(?Locale $locale): self
    {
        $this->locale = $locale;

        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getBanner(): ?Banner
    {
        return $this->banner;
    }

    public function setBanner(?Banner $banner): self
    {
        $this->banner = $banner;

        return $this;
    }
}