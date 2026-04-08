<?php 
namespace App\DTO;

readonly class BannerResponseDTO
{
    public function __construct(
        public int $id,
        public string $backgroundColor,
        public string $content,
    ) {}
}