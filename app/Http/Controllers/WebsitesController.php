<?php

namespace App\Http\Controllers;

use App\Http\Requests\Websites\CreateWebsiteRequest;
use App\Http\Requests\Websites\GetWebsitesRequest;
use App\Http\Requests\Websites\UpdateWebsiteRequest;
use App\Models\Website;
use Illuminate\Http\JsonResponse;

class WebsitesController extends Controller
{
    public function get(GetWebsitesRequest $request): JsonResponse
    {
        return response()->json(Website::all());
    }

    public function create(CreateWebsiteRequest $request): JsonResponse
    {
        $website = Website::create($request->validated());

        return response()->json($website, 201);
    }

    public function update(UpdateWebsiteRequest $request, Website $website): JsonResponse
    {
        $website->update($request->validated());

        return response()->json($website->fresh());
    }

    public function delete(Website $website): JsonResponse
    {
        $website->delete();

        return response()->json(null, 204);
    }
}
