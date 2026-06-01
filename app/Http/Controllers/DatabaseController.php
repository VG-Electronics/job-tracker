<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use App\Models\Offer;
use App\Models\Person;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DatabaseController extends Controller
{
    public function truncate(): JsonResponse
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Meeting::truncate();
        DB::table('offers_persons')->truncate();
        Offer::truncate();
        Person::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        return response()->json(['success' => true]);
    }
}
