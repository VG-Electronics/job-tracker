<?php

namespace App\Services;

use App\Scrappers\BrowserScrapper;
use App\Scrappers\HttpScrapper;

class ScrapperService
{
    public function __construct(
        private readonly HttpScrapper $httpScrapper,
        private readonly BrowserScrapper $browserScrapper,
    ) {}

    public function getOffers(string $url, ?string $offerUrlPart = null, bool $jsRender = false): array
    {
        $scrapper = $jsRender ? $this->browserScrapper : $this->httpScrapper;
        $raw = $scrapper->getOffers($url, $offerUrlPart);

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
