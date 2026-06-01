<?php

namespace App\Contracts;

interface ScrapperInterface
{
    public function getOffers(string $url, ?string $offerUrlPart = null): array;
}
