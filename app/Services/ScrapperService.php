<?php

namespace App\Services;

use App\Contracts\ScrapperInterface;

class ScrapperService
{
    public function __construct(
        private readonly ScrapperInterface $scrapper,
    ) {}

    public function getOffers(string $url, ?string $offerUrlPart = null): array
    {
        $raw = $this->scrapper->getOffers($url, $offerUrlPart);

        return array_values(array_filter(
            array_map(fn(array $offer) => $this->normalize($offer), $raw),
            fn(array $offer) => !empty($offer['url']) && !empty($offer['title']),
        ));
    }

    private function normalize(array $offer): array
    {
        return [
            'url' => $offer['url'] ?? '',
            'title' => trim($offer['title'] ?? ''),
            'description' => trim($offer['description'] ?? ''),
        ];
    }
}
