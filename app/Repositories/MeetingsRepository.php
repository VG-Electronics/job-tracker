<?php

namespace App\Repositories;

use App\Models\Meeting;
use Illuminate\Database\Eloquent\Collection;

class MeetingsRepository
{
    public function getMeetings(array $filters = [], array $sorting = [], int $limit = 50): Collection
    {
        $query = Meeting::with(['person', 'offer']);

        foreach ($filters as $column => $value) {
            $query->where($column, $value);
        }

        foreach ($sorting as $column => $direction) {
            $query->orderBy($column, $direction);
        }

        return $query->limit($limit)->get();
    }
}
