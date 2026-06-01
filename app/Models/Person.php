<?php

namespace App\Models;

use App\Enums\PersonRole;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'email', 'phone', 'role', 'linkedin_url'])]
class Person extends Model
{
    protected $table = 'persons';

    protected function casts(): array
    {
        return [
            'role' => PersonRole::class,
        ];
    }

    public function offers(): BelongsToMany
    {
        return $this->belongsToMany(Offer::class, 'offers_persons');
    }

    public function meetings(): HasMany
    {
        return $this->hasMany(Meeting::class);
    }
}
