<?php

namespace App\Scrappers;

use App\Contracts\ScrapperInterface;
use DOMDocument;
use DOMXPath;
use Illuminate\Support\Facades\Http;

class HttpScrapper implements ScrapperInterface
{
    public function getOffers(string $url, ?string $offerUrlPart = null): array
    {
        $response = Http::withHeaders([
            'User-Agent' => 'Mozilla/5.0 (compatible; JobTrackerBot/1.0)',
        ])->get($url);

        if (!$response->successful()) {
            return [];
        }

        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($response->body());
        libxml_clear_errors();

        $xpath = new DOMXPath($dom);
        $anchors = $xpath->query('//a[@href]');

        $base = $this->getBaseUrl($url);
        $offers = [];
        $seen = [];

        foreach ($anchors as $anchor) {
            $href = trim($anchor->getAttribute('href'));
            $title = trim($anchor->textContent);

            if (strlen($title) < 5 || empty($href) || str_starts_with($href, '#')) {
                continue;
            }

            $absoluteUrl = $this->resolveUrl($href, $base);

            if ($absoluteUrl === null || isset($seen[$absoluteUrl])) {
                continue;
            }

            if ($offerUrlPart !== null && !str_contains($absoluteUrl, $offerUrlPart)) {
                continue;
            }

            $description = $this->extractSiblingText($anchor, $xpath);

            $seen[$absoluteUrl] = true;
            $offers[] = [
                'url' => $absoluteUrl,
                'title' => $title,
                'description' => $description,
            ];
        }

        return $offers;
    }

    private function getBaseUrl(string $url): string
    {
        $parsed = parse_url($url);

        return $parsed['scheme'] . '://' . $parsed['host'];
    }

    private function resolveUrl(string $href, string $base): ?string
    {
        if (str_starts_with($href, 'http://') || str_starts_with($href, 'https://')) {
            return $href;
        }

        if (str_starts_with($href, '/')) {
            return $base . $href;
        }

        return null;
    }

    private function extractSiblingText(\DOMNode $node, DOMXPath $xpath): string
    {
        $parent = $node->parentNode;

        if ($parent === null) {
            return '';
        }

        return trim(preg_replace('/\s+/', ' ', $parent->textContent) ?? '');
    }
}
