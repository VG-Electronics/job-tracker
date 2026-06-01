<?php

namespace App\Repositories;

use App\Models\Website;

class WebsitesRepository
{
    public function getWebsites(): array
    {
        return Website::select('url', 'offer_url_part')->get()->toArray();
    }
}
