<?php

namespace App\Http\Requests\Persons;

use Illuminate\Foundation\Http\FormRequest;

class GetPersonsRequest extends FormRequest
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
