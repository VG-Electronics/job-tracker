<?php

namespace App\Http\Requests\Meetings;

use Illuminate\Foundation\Http\FormRequest;

class CreateMeetingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'offer_id'  => 'required|exists:offers,id',
            'person_id' => 'required|exists:persons,id',
            'occurs_at' => 'required|date',
            'title'     => 'required|string|max:255',
            'url'       => 'nullable|url',
            'note'      => 'nullable|string',
        ];
    }
}
