<?php

namespace App\Tests\Unit\Controller\Api;

use App\Controller\Api\BannerController;
use App\DTO\BannerResponseDTO;
use App\Service\GetActiveBannerService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Subclase testeable que evita la dependencia del contenedor de Symfony
 * sobreescribiendo el método json() de AbstractController.
 */
class TestableBannerController extends BannerController
{
    protected function json(mixed $data, int $status = 200, array $headers = [], array $context = []): JsonResponse
    {
        return new JsonResponse($data, $status, $headers);
    }
}

class BannerControllerTest extends TestCase
{
    /** @var \PHPUnit\Framework\MockObject\MockObject&GetActiveBannerService */
    private GetActiveBannerService $service;
    private TestableBannerController $controller;

    protected function setUp(): void
    {
        $this->service = $this->createMock(GetActiveBannerService::class);
        $this->controller = new TestableBannerController();
    }

    public function testIndexUsaLocaleEsPorDefecto(): void
    {
        $this->service
            ->expects($this->once())
            ->method('execute')
            ->with('es')
            ->willReturn([]);

        $request = new Request();
        $this->controller->index($request, $this->service);
    }

    public function testIndexUsaLangDeLaQueryString(): void
    {
        $this->service
            ->expects($this->once())
            ->method('execute')
            ->with('en')
            ->willReturn([]);

        $request = new Request(['lang' => 'en']);
        $this->controller->index($request, $this->service);
    }

    public function testIndexRetornaJsonConStatusSuccess(): void
    {
        $this->service->method('execute')->willReturn([]);

        $request = new Request();
        $response = $this->controller->index($request, $this->service);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(200, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('status', $data);
        $this->assertSame('success', $data['status']);
    }

    public function testIndexRetornaArrayDeBannersEnJson(): void
    {
        $dto = new BannerResponseDTO(1, '#ffffff', 'Banner de prueba');

        $this->service->method('execute')->willReturn([$dto]);

        $request = new Request();
        $response = $this->controller->index($request, $this->service);

        $data = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('banners', $data);
        $this->assertCount(1, $data['banners']);
        $this->assertSame(1, $data['banners'][0]['id']);
        $this->assertSame('#ffffff', $data['banners'][0]['backgroundColor']);
        $this->assertSame('Banner de prueba', $data['banners'][0]['content']);
    }

    public function testIndexRetornaArrayVacioCuandoNoHayBanners(): void
    {
        $this->service->method('execute')->willReturn([]);

        $request = new Request();
        $response = $this->controller->index($request, $this->service);

        $data = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('banners', $data);
        $this->assertSame([], $data['banners']);
    }

    public function testIndexRetornaMultiplesBanners(): void
    {
        $dtos = [
            new BannerResponseDTO(1, '#ff0000', 'Banner rojo'),
            new BannerResponseDTO(2, '#0000ff', 'Banner azul'),
        ];

        $this->service->method('execute')->willReturn($dtos);

        $request = new Request(['lang' => 'es']);
        $response = $this->controller->index($request, $this->service);

        $data = json_decode($response->getContent(), true);

        $this->assertCount(2, $data['banners']);
        $this->assertSame(2, $data['banners'][1]['id']);
        $this->assertSame('Banner azul', $data['banners'][1]['content']);
    }
}
