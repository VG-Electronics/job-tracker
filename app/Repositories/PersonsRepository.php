<?php

namespace App\Repositories;

use App\Models\Person;
use Illuminate\Database\Eloquent\Collection;

class PersonsRepository
{
    public function getPersons(): Collection
    {
        return Person::with(['meetings', 'offers'])->get();
    }
}
