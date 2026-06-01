<?php

namespace App\Http\Requests\Persons;

use App\Enums\PersonRole;
use Illuminate\Foundation\Http\FormRequest;

class CreatePersonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $roles = implode(',', array_column(PersonRole::cases(), 'value'));

        return [
            'offer_id'     => 'nullable|exists:offers,id',
            'name'         => 'required|string|max:255',
            'email'        => 'nullable|email',
            'phone'        => 'nullable|string|max:50',
            'role'         => "required|in:$roles",
            'linkedin_url' => 'nullable|url',
        ];
    }
}
