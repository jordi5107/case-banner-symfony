<?php 

namespace App\Service;

use App\DTO\BannerResponseDTO;
use App\Repository\BannerRepository;

class GetActiveBannerService
{
    public function __construct(
        private readonly BannerRepository $bannerRepository
    ) {}

    /**
     * @return BannerResponseDTO[]
     */
    public function execute(string $locale): array
    {
        $now = new \DateTimeImmutable();
        $banners = $this->bannerRepository->findActiveBanners($locale, $now);

        $dtos = [];
        foreach ($banners as $banner) {
            $translation = $banner->getTranslations()->filter(
                fn($t) => $t->getLocale()?->getCode() === $locale
            )->first();

            if ($translation) {
                $dtos[] = new BannerResponseDTO(
                    $banner->getId(),
                    $banner->getBackgroundColor(),
                    $translation->getContent()
                );
            }
        }

        return $dtos;
    }
}