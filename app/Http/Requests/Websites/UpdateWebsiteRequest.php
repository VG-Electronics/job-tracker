<?php

namespace App\Http\Requests\Websites;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWebsiteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'           => 'string|max:255',
            'url'            => 'url',
            'offer_url_part' => 'nullable|string|max:255',
            'js_render'      => 'boolean',
        ];
    }
}
