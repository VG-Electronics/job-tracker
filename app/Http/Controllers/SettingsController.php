<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function get(): JsonResponse
    {
        return response()->json([
            'ai_additional_instruction' => AppSetting::getValue('ai_additional_instruction'),
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ai_additional_instruction' => 'nullable|string|max:2000',
        ]);

        AppSetting::setValue('ai_additional_instruction', $validated['ai_additional_instruction'] ?? null);

        return response()->json([
            'ai_additional_instruction' => $validated['ai_additional_instruction'] ?? null,
        ]);
    }
}
