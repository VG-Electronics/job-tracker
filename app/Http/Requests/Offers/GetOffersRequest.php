<?php

namespace App\Http\Requests\Offers;

use App\Enums\OfferStatus;
use App\Enums\SalaryType;
use Illuminate\Foundation\Http\FormRequest;

class GetOffersRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $statuses = implode(',', array_column(OfferStatus::cases(), 'value'));
        $salaryTypes = implode(',', array_column(SalaryType::cases(), 'value'));

        return [
            'page'        => 'integer|min:1',
            'per_page'    => 'integer|min:1|max:100',
            'sort_by'     => 'in:date,salary',
            'sort_dir'    => 'in:asc,desc',
            'status'      => "in:$statuses",
            'min_salary'  => 'integer|min:0',
            'salary_type' => "in:$salaryTypes",
            'search'      => 'nullable|string|max:255',
            'starred'     => 'nullable|boolean',
        ];
    }
}
