<?php

namespace App\Http\Requests\Offers;

use App\Enums\OfferStatus;
use App\Enums\SalaryType;
use Illuminate\Foundation\Http\FormRequest;

class UpdateOfferRequest extends FormRequest
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
            'status'               => "in:$statuses",
            'title'                => 'string|max:255',
            'company'              => 'nullable|string|max:255',
            'recruitment_company'  => 'nullable|string|max:255',
            'description'          => 'nullable|string',
            'url'                  => 'url',
            'min_salary'           => 'nullable|integer|min:0',
            'max_salary'           => 'nullable|integer|min:0',
            'salary_type'          => "nullable|in:$salaryTypes",
            'note'                 => 'nullable|string',
        ];
    }
}
