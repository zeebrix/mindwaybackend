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
            $user = $this->googleProvider->getUser();
            $counsellor = Counselor::Where('id', session('google_action_user_id'))->first();
            $counsellor->google_id = $user->id;
            $counsellor->google_name = $user->name;
            $counsellor->google_email = $user->email;
            $counsellor->google_picture = $user->picture;
            $counsellor->save();
            if(auth()->check())
            {
                return redirect()->route('admin.counsellor.profile',$counsellor->id);
            }
            return redirect()->route('counseller.profile');
        } catch (\Exception $e) {
            if(auth()->check())
            {
                return redirect()->route('admin.counsellor.profile',$counsellor->id);
            }
            return redirect()->route('counseller.profile');
        }
    }
    public function listEvents(Request $request)
    {
        $gooleToken = GoogleToken::first();
        try {
            $timeMin = now()->toRfc3339String();
            $timeMax = now()->endOfYear()->toRfc3339String();
            $events = $this->googleProvider->getAllEvents($gooleToken->access_token, $timeMin, $timeMax);
            return response()->json($events);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function handleWebhook(Request $request)
    {
        \Log::info('Google Calendar Webhook Triggered', $request->all());

        // Fetch updated events
        // $counselor = Counselor::where('google_calendar_id', $request->get('calendarId'))->first();

        // if (!$counselor || !$counselor->googleToken) {
        //     return response()->json(['message' => 'No counselor found'], 404);
        // }
        // app(SlotGenerationService::class)->removeConflictingSlots($counselor, now()->month);

        return response()->json(['message' => 'Processed'], 200);
}


}
