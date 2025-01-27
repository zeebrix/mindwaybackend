<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Counselor;
use App\Services\GoogleProvider;
use Illuminate\Http\Request;
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
}
