<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Models\UserPreference;
class UserPreferenceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $preferences = UserPreference::where('user_id', $request->user_id)->first();

        return response()->json($preferences ?? null);
    }
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'specializations' => 'nullable|array',
            'specializations.*' => 'string',
            'location' => 'nullable|string',
            'language' => 'nullable|string',
            'gender' => 'nullable|string',
            'communication_methods' => 'nullable|array',
            'communication_methods.*' => 'string',
        ]);

        $preferences = UserPreference::updateOrCreate(
            ['user_id' => $request->user_id],
            $validated
        );

        return response()->json(['message' => 'Preferences saved successfully!', 'data' => $preferences]);
    }
    public function destroy(Request $request): JsonResponse
    {
        UserPreference::where('user_id', $request->user_id)->delete();

        return response()->json(['message' => 'Preferences deleted successfully!']);
    }
}
