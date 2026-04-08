<?php

namespace App\Controller\Api;

use App\Service\GetActiveBannerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class BannerController extends AbstractController
{
    #[Route('/api/banners', name: 'api_banners')]
    public function index(Request $request, GetActiveBannerService $getActiveBannersService)
    {
        $locale = $request->query->get('lang', 'es');
        $banners = $getActiveBannersService->execute($locale);

        return $this->json([
            'status' => 'success',
            'banners' => $banners
        ]);
    }

    #[Route('/banners', name: 'show_banners')]
    public function showRenderBanners(Request $request, GetActiveBannerService $getActiveBannersService)
    {
        $locale = $request->query->get('lang', 'es');
        $banners = $getActiveBannersService->execute($locale);

        return $this->render('banners/banners.html.twig', [
            'banners' => $banners
        ]);
    }   
}