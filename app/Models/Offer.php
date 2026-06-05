<?php

namespace App\Models;

use App\Enums\OfferStatus;
use App\Enums\SalaryType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['status', 'title', 'company', 'recruitment_company', 'description', 'url', 'min_salary', 'max_salary', 'salary_type', 'note', 'is_starred', 'applied_at'])]
class Offer extends Model
{
    protected function casts(): array
    {
        return [
            'status'     => OfferStatus::class,
            'salary_type' => SalaryType::class,
            'is_starred' => 'boolean',
            'applied_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::updating(function (Offer $offer) {
            if ($offer->isDirty('status') && $offer->status === OfferStatus::Applied && $offer->applied_at === null) {
                $offer->applied_at = Carbon::now('Europe/Warsaw');
            }
        });
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
