<?php

namespace App\Http\Controllers;

use App\Http\Requests\Persons\CreatePersonRequest;
use App\Http\Requests\Persons\GetPersonsRequest;
use App\Http\Requests\Persons\UpdatePersonRequest;
use App\Models\Person;
use Illuminate\Http\JsonResponse;

class PersonsController extends Controller
{
    public function get(GetPersonsRequest $request): JsonResponse
    {
        return response()->json(Person::with('offers')->get());
    }

    public function create(CreatePersonRequest $request): JsonResponse
    {
        $person = Person::create($request->safe()->except('offer_id'));

        if ($offerId = $request->validated()['offer_id'] ?? null) {
            $person->offers()->attach($offerId);
        }

        return response()->json($person, 201);
    }

    public function update(UpdatePersonRequest $request, Person $person): JsonResponse
    {
        $person->update($request->validated());

        return response()->json($person->fresh());
    }

    public function delete(Person $person): JsonResponse
    {
        $person->delete();

        return response()->json(null, 204);
    }
}
