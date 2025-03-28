<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Models\UserPreference;
use Illuminate\Support\Facades\Validator;

class UserPreferenceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $preferences = UserPreference::where('user_id', $request->user_id)->first();
        $preferences['gender'] ??= ['Male', 'Female'];
        return response()->json(['data' => $preferences,'status' => true]);
    }
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'specializations' => 'nullable|array',
            'specializations.*' => 'nullable|string',
            'location' => 'nullable|string',
            'language' => 'nullable|string',
            'gender' => 'nullable|array',
            'gender.*' => 'nullable|string',
            'communication_methods' => 'nullable|array',
            'communication_methods.*' => 'nullable|string',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()
            ], 422);
        }
    
        // Validation passed, save preferences
        $validated = $validator->validated();
        $validated['specializations'] = $validated['specializations'] ?? [];
        $validated['gender'] = $validated['gender'] ?? [];
        $validated['communication_methods'] = $validated['communication_methods'] ?? [];
        $preferences = UserPreference::updateOrCreate(
            ['user_id' => $request->user_id],
            $validated
        );

        return response()->json(['status' => true,'message' => 'Preferences saved successfully!', 'data' => $preferences]);
    }
    public function destroy(Request $request): JsonResponse
    {
        UserPreference::where('user_id', $request->user_id)->delete();

        return response()->json(['status' => true,'message' => 'Preferences deleted successfully!']);
    }
}
