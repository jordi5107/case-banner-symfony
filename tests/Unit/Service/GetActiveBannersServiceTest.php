<?php

namespace App\Tests\Unit\Service;

use App\DTO\BannerResponseDTO;
use App\Entity\Banner;
use App\Entity\BannerTranslation;
use App\Entity\Locale;
use App\Repository\BannerRepository;
use App\Service\GetActiveBannerService;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class GetActiveBannersServiceTest extends TestCase
{
private BannerRepository $repository;
    private GetActiveBannerService $service;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(BannerRepository::class);
        $this->service = new GetActiveBannerService($this->repository);
    }

    public function testExecuteReturnsEmptyArrayWhenNoBannersFound(): void
    {
        $this->repository
            ->expects($this->once())
            ->method('findActiveBanners')
            ->willReturn([]);

        $result = $this->service->execute('es');

        $this->assertSame([], $result);
    }

    public function testExecuteReturnsDTOsForMatchingLocale(): void
    {
        $locale = $this->createMock(Locale::class);
        $locale->method('getCode')->willReturn('es');

        $translation = $this->createMock(BannerTranslation::class);
        $translation->method('getLocale')->willReturn($locale);
        $translation->method('getContent')->willReturn('Contenido en español');

        $banner = $this->createMock(Banner::class);
        $banner->method('getId')->willReturn(1);
        $banner->method('getBackgroundColor')->willReturn('#ff0000');
        $banner->method('getTranslations')->willReturn(new ArrayCollection([$translation]));

        $this->repository
            ->expects($this->once())
            ->method('findActiveBanners')
            ->willReturn([$banner]);

        $result = $this->service->execute('es');

        $this->assertCount(1, $result);
        $this->assertInstanceOf(BannerResponseDTO::class, $result[0]);
        $this->assertSame(1, $result[0]->id);
        $this->assertSame('#ff0000', $result[0]->backgroundColor);
        $this->assertSame('Contenido en español', $result[0]->content);
    }

    public function testExecuteSkipsBannerWithoutMatchingLocaleTranslation(): void
    {
        $locale = $this->createMock(Locale::class);
        $locale->method('getCode')->willReturn('en');

        $translation = $this->createMock(BannerTranslation::class);
        $translation->method('getLocale')->willReturn($locale);
        $translation->method('getContent')->willReturn('Content in English');

        $banner = $this->createMock(Banner::class);
        $banner->method('getId')->willReturn(2);
        $banner->method('getBackgroundColor')->willReturn('#00ff00');
        $banner->method('getTranslations')->willReturn(new ArrayCollection([$translation]));

        $this->repository
            ->expects($this->once())
            ->method('findActiveBanners')
            ->willReturn([$banner]);

        $result = $this->service->execute('es');

        $this->assertSame([], $result);
    }

    public function testExecuteReturnsOnlyBannersWithMatchingLocale(): void
    {
        $localeEs = $this->createMock(Locale::class);
        $localeEs->method('getCode')->willReturn('es');

        $localeEn = $this->createMock(Locale::class);
        $localeEn->method('getCode')->willReturn('en');

        $translationEs = $this->createMock(BannerTranslation::class);
        $translationEs->method('getLocale')->willReturn($localeEs);
        $translationEs->method('getContent')->willReturn('Hola');

        $translationEn = $this->createMock(BannerTranslation::class);
        $translationEn->method('getLocale')->willReturn($localeEn);
        $translationEn->method('getContent')->willReturn('Hello');

        $bannerWithEs = $this->createMock(Banner::class);
        $bannerWithEs->method('getId')->willReturn(1);
        $bannerWithEs->method('getBackgroundColor')->willReturn('#aabbcc');
        $bannerWithEs->method('getTranslations')->willReturn(new ArrayCollection([$translationEs]));

        $bannerWithoutEs = $this->createMock(Banner::class);
        $bannerWithoutEs->method('getId')->willReturn(2);
        $bannerWithoutEs->method('getBackgroundColor')->willReturn('#112233');
        $bannerWithoutEs->method('getTranslations')->willReturn(new ArrayCollection([$translationEn]));

        $this->repository
            ->expects($this->once())
            ->method('findActiveBanners')
            ->willReturn([$bannerWithEs, $bannerWithoutEs]);

        $result = $this->service->execute('es');

        $this->assertCount(1, $result);
        $this->assertSame(1, $result[0]->id);
        $this->assertSame('Hola', $result[0]->content);
    }

    public function testExecutePassesLocaleAndCurrentDateToRepository(): void
    {
        $this->repository
            ->expects($this->once())
            ->method('findActiveBanners')
            ->with(
                'fr',
                $this->isInstanceOf(\DateTimeImmutable::class)
            )
            ->willReturn([]);

        $this->service->execute('fr');
    }
}
