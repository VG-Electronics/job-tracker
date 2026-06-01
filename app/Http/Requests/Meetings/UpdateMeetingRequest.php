<?php

namespace App\Http\Requests\Meetings;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMeetingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'person_id' => 'exists:persons,id',
            'occurs_at' => 'date',
            'title'     => 'string|max:255',
            'url'       => 'nullable|url',
            'note'      => 'nullable|string',
        ];
    }
}
