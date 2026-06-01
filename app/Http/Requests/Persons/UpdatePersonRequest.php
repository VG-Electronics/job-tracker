<?php

namespace App\Http\Requests\Persons;

use App\Enums\PersonRole;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePersonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $roles = implode(',', array_column(PersonRole::cases(), 'value'));

        return [
            'name'         => 'string|max:255',
            'email'        => 'nullable|email',
            'phone'        => 'nullable|string|max:50',
            'role'         => "in:$roles",
            'linkedin_url' => 'nullable|url',
        ];
    }
}
