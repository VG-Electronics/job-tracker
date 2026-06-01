<?php

namespace App\Http\Requests\Websites;

use Illuminate\Foundation\Http\FormRequest;

class GetWebsitesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [];
    }
}
