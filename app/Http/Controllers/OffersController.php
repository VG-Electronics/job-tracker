<?php

namespace App\Http\Controllers;

use App\Http\Requests\Offers\FetchNewOffersRequest;
use App\Http\Requests\Offers\GetOffersRequest;
use App\Http\Requests\Offers\UpdateOfferRequest;
use App\Jobs\FetchNewOffersJob;
use App\Models\Offer;
use Illuminate\Http\JsonResponse;

class OffersController extends Controller
{
    public function get(GetOffersRequest $request): JsonResponse
    {
        $query = Offer::query();

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        if ($request->filled('min_salary')) {
            $query->where('min_salary', '>=', $request->integer('min_salary'));
        }

        if ($salaryType = $request->query('salary_type')) {
            $query->where('salary_type', $salaryType);
        }

        if ($request->boolean('starred')) {
            $query->where('is_starred', true);
        }

        if ($search = $request->query('search')) {
            $like = '%' . $search . '%';
            $query->where(function ($q) use ($like) {
                $q->where('title', 'like', $like)
                  ->orWhere('description', 'like', $like)
                  ->orWhere('company', 'like', $like)
                  ->orWhere('recruitment_company', 'like', $like);
            });
        }

        $sortBy = $request->query('sort_by') === 'salary' ? 'min_salary' : 'created_at';
        $sortDir = $request->query('sort_dir', 'desc');

        $offers = $query->with(['persons', 'meetings.person'])
            ->orderByDesc('is_starred')
            ->orderBy($sortBy, $sortDir)
            ->paginate($request->integer('per_page', 15));

        return response()->json($offers);
    }

    public function fetchNewOffers(FetchNewOffersRequest $request): JsonResponse
    {
        $before = Offer::count();
        FetchNewOffersJob::dispatchSync();
        $count = Offer::count() - $before;

        return response()->json(['count' => $count]);
    }

    public function update(UpdateOfferRequest $request, Offer $offer): JsonResponse
    {
        $offer->update($request->validated());

        return response()->json($offer->fresh());
    }
}
