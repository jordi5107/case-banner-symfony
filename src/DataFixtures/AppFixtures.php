<?php

namespace App\DataFixtures;

use App\Entity\Banner;
use App\Entity\BannerTranslation;
use App\Entity\Locale;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $now = new \DateTimeImmutable();

        // Locales disponibles: activos e inactivos
        $localesData = [
            ['code' => 'es', 'active' => true],
            ['code' => 'en', 'active' => true],
            ['code' => 'fr', 'active' => false],
        ];

        $locales = [];
        foreach ($localesData as $localeData) {
            $locale = new Locale();
            $locale->setCode($localeData['code']);
            $locale->setActive($localeData['active']);
            $manager->persist($locale);
            $locales[$localeData['code']] = $locale;
        }

        // Banners con sus traducciones
        $bannersData = [
            [
                'internalName'    => 'Promo Primavera 2026',
                'backgroundColor' => '#e8f5e9',
                'active'          => true,
                'startDate'       => $now->modify('-7 days'),
                'endDate'         => $now->modify('+30 days'),
                'translations'    => [
                    ['locale' => 'es', 'content' => 'Descubre nuestra coleccion de primavera! Productos naturales para renovar tu rutina.'],
                    ['locale' => 'en', 'content' => 'Discover our spring collection! Natural products to refresh your routine.'],
                    ['locale' => 'fr', 'content' => 'Decouvrez notre collection de printemps! Produits naturels pour renouveler votre routine.'],
                ],
            ],
            [
                'internalName'    => 'Flash Sale 48h',
                'backgroundColor' => '#fff3e0',
                'active'          => true,
                'startDate'       => $now->modify('-1 day'),
                'endDate'         => $now->modify('+1 day'),
                'translations'    => [
                    ['locale' => 'es', 'content' => 'Flash Sale 48h - 20% de descuento en toda la tienda. Solo hasta manana!'],
                    ['locale' => 'en', 'content' => 'Flash Sale 48h - 20% off storewide. Today and tomorrow only!'],
                ],
            ],
            [
                'internalName'    => 'Envio gratis Mayo',
                'backgroundColor' => '#e3f2fd',
                'active'          => true,
                'startDate'       => $now->modify('+1 day'),
                'endDate'         => $now->modify('+45 days'),
                'translations'    => [
                    ['locale' => 'es', 'content' => 'Este mes, envio gratuito en todos tus pedidos a partir de 30 euros.'],
                    ['locale' => 'en', 'content' => 'This month, free shipping on all orders over 30 euros.'],
                ],
            ],
            [
                'internalName'    => 'Campana Invierno 2025 (expirada)',
                'backgroundColor' => '#ede7f6',
                'active'          => true,
                'startDate'       => $now->modify('-60 days'),
                'endDate'         => $now->modify('-5 days'),
                'translations'    => [
                    ['locale' => 'es', 'content' => 'Cuidate este invierno con nuestros productos hidratantes.'],
                    ['locale' => 'en', 'content' => 'Take care this winter with our hydrating products.'],
                ],
            ],
            [
                'internalName'    => 'Test banner inactivo',
                'backgroundColor' => '#fce4ec',
                'active'          => false,
                'startDate'       => $now->modify('-3 days'),
                'endDate'         => $now->modify('+10 days'),
                'translations'    => [
                    ['locale' => 'es', 'content' => 'Este banner esta desactivado y no debe mostrarse.'],
                ],
            ],
        ];

        foreach ($bannersData as $data) {
            $banner = new Banner();
            $banner->setInternalName($data['internalName']);
            $banner->setBackgroundColor($data['backgroundColor']);
            $banner->setActive($data['active']);
            $banner->setStartDate($data['startDate']);
            $banner->setEndDate($data['endDate']);

            foreach ($data['translations'] as $t) {
                if (!isset($locales[$t['locale']])) {
                    continue;
                }
                $translation = new BannerTranslation();
                $translation->setLocale($locales[$t['locale']]);
                $translation->setContent($t['content']);
                $banner->addTranslation($translation);
            }

            $manager->persist($banner);
        }

        $manager->flush();
    }
}
