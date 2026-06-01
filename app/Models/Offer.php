<?php

namespace App\Models;

use App\Enums\OfferStatus;
use App\Enums\SalaryType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['status', 'title', 'company', 'recruitment_company', 'description', 'url', 'min_salary', 'max_salary', 'salary_type', 'note'])]
class Offer extends Model
{
    protected function casts(): array
    {
        return [
            'status' => OfferStatus::class,
            'salary_type' => SalaryType::class,
        ];
    }

    public function persons(): BelongsToMany
    {
        return $this->belongsToMany(Person::class, 'offers_persons');
    }

    public function meetings(): HasMany
    {
        return $this->hasMany(Meeting::class);
    }
}
