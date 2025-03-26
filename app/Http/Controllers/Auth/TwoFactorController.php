<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Counselor;
use App\Models\Program;
use App\Models\User;
use App\Services\Calendar\CalendarManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FA\Google2FA;
use Illuminate\Validation\ValidationException;

class TwoFactorController extends Controller
{
    public function auth(string $driver): RedirectResponse
    {
        try {
            return app(CalendarManager::class)->driver($driver)->redirect();
        } catch (\InvalidArgumentException $exception) {
            report($exception);

            abort(400, $exception->getMessage());
        }
    }
    public function resetPassword(Request $request, $type)
    {
        return view('reset-password')->with(['type' => $type]);
    }
    public function submitResetPassword(Request $request)
    {
        return view('reset-change-password');
    }
    public function show(Request $request)
    {
        return view('2fa');
    }
    public function counselorShow(Request $request)
    {
        return view('counselor.2fa');
    }
    public function programShow()
    {
        return view('program.2fa');   
    }
    public function verify(Request $request)
    {
        $request->validate([
            'one_time_password' => 'required|string',
        ]);

        $user_id = $request->session()->get('2fa:user:id');
        $remember = $request->session()->get('2fa:auth:remember', false);
        $attempt = $request->session()->get('2fa:auth:attempt', false);
        if (!$user_id) {
            return redirect()->route('login');
        }

        $user = User::find($user_id);

        if (!$user || !$user->uses_two_factor_auth) {
            return redirect()->route('login');
        }

        $google2fa = new Google2FA();
        $otp_secret = $user->google2fa_secret;
        if (!$google2fa->verifyKey($otp_secret, $request->one_time_password)) {
            $request->session()->put('2fa:user:id', $user_id);
            $request->session()->put('2fa:auth:attempt', true);
            throw ValidationException::withMessages([
                'one_time_password' => [__('The one time password is invalid.')],
            ]);
        }

        $guard = config('auth.defaults.guard');
        $credentials = [$user->getAuthIdentifierName() => $user->getAuthIdentifier(), 'password' => $user->getAuthPassword()];

        if ($remember) {
            $guard = config('auth.defaults.remember_me_guard', $guard);
        }

        if ($attempt) {
            $guard = config('auth.defaults.attempt_guard', $guard);
        }

        if (Auth::guard($guard)->attempt($credentials, $remember)) {
            $request->session()->remove('2fa:user:id');
            $request->session()->remove('2fa:auth:remember');
            $request->session()->remove('2fa:auth:attempt');

            return redirect()->intended('/login');
        }

        return redirect()->route('login')->withErrors([
            'password' => __('The provided credentials are incorrect.'),
        ]);
    }
    public function counselorVerify(Request $request)
    {
        $request->validate([
            'one_time_password' => 'required|string',
        ]);

        $user_id = $request->session()->get('2fa:user:id');
        $remember = $request->session()->get('2fa:auth:remember', false);
        $attempt = $request->session()->get('2fa:auth:attempt', false);
        if (!$user_id) {
            return redirect()->route('counseller.login');
        }

        $user = Counselor::find($user_id);

        if (!$user || !$user->uses_two_factor_auth) {
            return redirect()->route('counseller.login');
        }

        $google2fa = new Google2FA();
        $otp_secret = $user->google2fa_secret;
        if (!$google2fa->verifyKey($otp_secret, $request->one_time_password)) {
            $request->session()->put('2fa:user:id', $user_id);
            $request->session()->put('2fa:auth:attempt', true);
            throw ValidationException::withMessages([
                'one_time_password' => [__('The one time password is invalid.')],
            ]);
        }
        Auth::guard('counselor')->login($user);
        if (Auth::guard('counselor')->user()) {
            $request->session()->remove('2fa:user:id');
            $request->session()->remove('2fa:auth:remember');
            $request->session()->remove('2fa:auth:attempt');
            return redirect()->route('counseller.dashboard');
        }
        return redirect()->route('counseller.login')->withErrors([
            'password' => __('The provided credentials are incorrect.'),
        ]);
    }
    public function programVerify(Request $request)
    {
        $request->validate([
            'one_time_password' => 'required|string',
        ]);

        $user_id = $request->session()->get('2fa:user:id');
        $remember = $request->session()->get('2fa:auth:remember', false);
        $attempt = $request->session()->get('2fa:auth:attempt', false);
        if (!$user_id) {
            return redirect()->route('program.login');
        }

        $program = Program::find($user_id);

        if (!$program || !$program->is_2fa_enabled) {
            return redirect()->route('program.login');
        }

        $google2fa = new Google2FA();
        $otp_secret = $program->google2fa_secret;
        if (!$google2fa->verifyKey($otp_secret, $request->one_time_password)) {
            $request->session()->put('2fa:user:id', $user_id);
            $request->session()->put('2fa:auth:attempt', true);
            throw ValidationException::withMessages([
                'one_time_password' => [__('The one time password is invalid.')],
            ]);
        }
        Auth::guard('programs')->login($program);
        session()->forget('user_id');
        if (Auth::guard('programs')->check()) {
            $request->session()->remove('2fa:user:id');
            $request->session()->remove('2fa:auth:remember');
            $request->session()->remove('2fa:auth:attempt');
            return redirect()->route('program.dashboard');
        }

        return redirect()->route('program.login')->withErrors([
            'password' => __('The provided credentials are incorrect.'),
        ]);
    }
}
