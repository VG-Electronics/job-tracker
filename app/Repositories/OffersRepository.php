<?php

namespace App\Repositories;

use App\Models\Offer;
use Illuminate\Database\Eloquent\Collection;

class OffersRepository
{
    public function getOffers(array $filters = [], array $sorting = [], int $limit = 50): Collection
    {
        $query = Offer::with(['meetings', 'persons']);

        foreach ($filters as $column => $value) {
            $query->where($column, $value);
        }

        foreach ($sorting as $column => $direction) {
            $query->orderBy($column, $direction);
        }

        return $query->limit($limit)->get();
    }

    public function getExistingUrls(array $urls): array
    {
        return Offer::whereIn('url', $urls)->pluck('url')->toArray();
    }
}
