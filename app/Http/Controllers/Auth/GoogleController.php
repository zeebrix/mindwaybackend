<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Counselor;
use App\Models\GoogleToken;
use App\Services\GoogleProvider;
use App\Services\SlotGenerationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class GoogleController extends Controller
{
    protected $googleProvider;

    public function __construct(GoogleProvider $googleProvider)
    {
        $this->googleProvider = $googleProvider;
    }

    public function redirectToGoogle(Request $request)
    {
        Session::put('google_action_user_id', $request->id);
        return redirect()->to($this->googleProvider->createAuthUrl());
    }

    public function handleGoogleCallback(Request $request)
    {
        try {
            $counsellor = Counselor::Where('id', session('google_action_user_id'))->first();
            $user = $this->googleProvider->getUser();
            $counsellor->google_id = $user->id;
            $counsellor->google_name = $user->name;
            $counsellor->google_email = $user->email;
            $counsellor->google_picture = $user->picture;
            $counsellor->save();
            if ($counsellor->googleToken) {
                app(GoogleProvider::class)->watchCalendar($counsellor);
            }
            if(auth()->check())
            {
                return redirect()->route('admin.counsellor.profile',$counsellor->id);
            }
            return redirect()->route('counseller.profile');
        }catch (\RuntimeException $e) {
            Log::error("Google OAuth Runtime Error: " . $e->getMessage());
            if(auth()->check())
            {
                return redirect()->route('admin.counsellor.profile',$counsellor->id)->with('error', $e->getMessage());
            }
            return redirect()->route('counseller.profile')->with('error', $e->getMessage());
        }catch (\Exception $e) {
            if(auth()->check())
            {
                return redirect()->route('admin.counsellor.profile',$counsellor->id);
            }
            return redirect()->route('counseller.profile');
        }
    }
    public function listEvents(Request $request)
    {
        $counsellor = Counselor::where('id',$request->id)->first();
        if($counsellor && $counsellor->googleToken)
        {
            $googleToken = $counsellor->googleToken;
        }else
        {
            $googleToken = GoogleToken::first();
        }
        try {
            $timeMin = now()->toRfc3339String();
            $timeMax = now()->endOfYear()->toRfc3339String();
            $events = $this->googleProvider->getAllEvents($googleToken->access_token, $timeMin, $timeMax);
            return response()->json($events);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function handleWebhookOLD(Request $request)
    {
        Log::info('Google Calendar Webhook Triggered', [
            'headers' => $request->headers->all(),
            'body' => $request->getContent(), // Raw JSON payload
            'query' => $request->query(), // Query parameters
            'post' => $request->post(), // Form data
        ]);
        $resourceIdHeader = $request->header('X-Goog-Resource-ID')??$request->header('x-goog-resource-id');
        $resourceId = is_array($resourceIdHeader) ? $resourceIdHeader[0] : $resourceIdHeader;
        if (!$resourceId) {
            Log::error("Google Webhook: Missing X-Goog-Resource-ID header.");
            return response()->json(['message' => 'Invalid webhook request'], 400);
        }
        $counselor = Counselor::where('google_webhook_data', 'LIKE', '%"resource_id":"'.$resourceId.'"%')->first();
        
        if (!$counselor) {
            Log::error("No matching counselor found for webhook: $resourceId");
            return response()->json(['message' => 'Invalid webhook resource ID'], 404);
        }
    
        // Fetch latest events
        
        app(SlotGenerationService::class)->removeConflictingSlots($counselor);
        app(SlotGenerationService::class)->restoreAvailableSlots($counselor);

    
        return response()->json(['message' => 'Webhook processed']);

        // Fetch updated events
        // $counselor = Counselor::where('google_calendar_id', $request->get('calendarId'))->first();

        // if (!$counselor || !$counselor->googleToken) {
        //     return response()->json(['message' => 'No counselor found'], 404);
        // }

        return response()->json(['message' => 'Processed'], 200);
    }
    public function handleWebhook(Request $request)
    {
        Log::info('Google Calendar Webhook Triggered', [
            'headers' => $request->headers->all(),
            'body' => $request->getContent(), // Raw JSON payload
            'query' => $request->query(), // Query parameters
            'post' => $request->post(), // Form data
        ]);

        // Extract Resource ID
        $resourceIdHeader = $request->header('X-Goog-Resource-ID') ?? $request->header('x-goog-resource-id');
        $resourceId = is_array($resourceIdHeader) ? $resourceIdHeader[0] : $resourceIdHeader;

        if (!$resourceId) {
            Log::error("Google Webhook: Missing X-Goog-Resource-ID header.");
            return response()->json(['message' => 'Invalid webhook request'], 400);
        }
        
        Log::info("Google Webhook: Extracted Resource ID: $resourceId");

        // Search for the counselor in the database
        $counselor = Counselor::where('google_webhook_data', 'LIKE', '%"resource_id":"'.$resourceId.'"%')->first();
        
        if (!$counselor) {
            Log::error("No matching counselor found for webhook: $resourceId");
            return response()->json(['message' => 'Invalid webhook resource ID'], 404);
        }

        Log::info("Google Webhook: Counselor found with ID: {$counselor->id}");

        // Fetch latest events and remove conflicting slots
        try {
            app(SlotGenerationService::class)->removeConflictingSlots($counselor);
            app(SlotGenerationService::class)->restoreAvailableSlots($counselor);
            Log::info("Google Webhook: Successfully removed conflicting slots for Counselor ID: {$counselor->id}");
        } catch (\Exception $e) {
            Log::error("Google Webhook: Error in removing conflicting slots", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['message' => 'Error processing webhook'], 500);
        }

        return response()->json(['message' => 'Webhook processed'], 200);
    }



}
