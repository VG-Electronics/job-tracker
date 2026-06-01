<?php

namespace App\Http\Requests\Websites;

use Illuminate\Foundation\Http\FormRequest;

class CreateWebsiteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'           => 'required|string|max:255',
            'url'            => 'required|url',
            'offer_url_part' => 'nullable|string|max:255',
        ];
    }
}
