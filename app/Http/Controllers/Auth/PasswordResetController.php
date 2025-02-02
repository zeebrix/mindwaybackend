<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Counselor;
use App\Models\CustomreBrevoData;
use App\Models\Program;
use App\Models\ProgramMultiLogin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Exception\Auth\UserNotFound;
use Kreait\Firebase\Exception\AuthException;
use Illuminate\Support\Carbon;

class PasswordResetController extends Controller
{
    public function showCounsellorSetPassword()
    {
        return view('auth.counsellor-password');
    }

    public function counsellorPasswordSet(Request $request)
    {
        $request->validate(['password' => 'required|min:8']);
        $counselorId = decrypt($request->counsellorId);
        $counsellor = Counselor::where('id', $counselorId)->first();
        if ($counsellor) {
            $counsellor->password = bcrypt($request->input('password'));
            $counsellor->save();
        }
        $route = '/counsellinglogin';
        return redirect($route)->with('status', 'Password successfully reset.');
    }

    public function showForgotPasswordForm($type)
    {
        return view('auth.forgot-password')->with(['type' => $type]);
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $type = $request->type ?? 'admin';
        $email = $request->input('email');
        $user = $customer = $counselor = null;
        if ($type == 'admin') {
            $user = DB::table('users')->where('email', $email)->first();
        } elseif ($type == 'program') {
            $customer = CustomreBrevoData::where('email',$email)->where('level','admin')->first();
        } elseif ($type == 'counsellor') {
            $counselor = DB::table('counselors')->where('email', $email)->first();
        }
        $account = $user ?? $customer ?? $counselor;

        if (!$account) {
            return back()->withErrors(['email' => 'No account found with this email address.']);
        }

        // Generate a unique token
        $token = Str::random(64);

        // Save token to a custom table or `password_resets`
        DB::table('password_resets')->updateOrInsert(
            ['email' => $email], // Find by email
            [
                'email' => $email,
                'token' => $token,
                'created_at' => now(),
            ]
        );

        // Generate the reset link
        $resetLink = url("/reset-password/{$token}?email={$email}&type={$type}");
        // Send the reset link to the user (email)
        // Here you can use Laravel Mail to send the reset link
        \Mail::to($email)->send(new \App\Mail\ResetPasswordMail($resetLink));
        return back()->with(['message' => "Mail sent Successfully."]);
    }

    public function showResetPasswordForm(Request $request, $token)
    {
        $email = $request->email;
        $type = $request->type ?? 'admin';
        return view('auth.reset-password', ['token' => $token, 'email' => $email, 'type' => $type]);
    }
    public function showResetPasswordAppForm(Request $request, $token)
    {
        $email = $request->email;
        $type = $request->type ?? 'admin';
        return view('auth.reset-app-password', ['token' => $token, 'email' => $email, 'type' => $type]);
    }
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);
        $type = $request->type;
        $resetRecord = DB::table('password_resets')
            ->where('email', $request->input('email'))
            ->where('token', $request->input('token'))
            ->first();
        if (!$resetRecord) {
            return back()->withErrors(['email' => 'Invalid or expired reset token.']);
        }
        $expiresAt = now()->subMinutes(60);
        if ($resetRecord->created_at < $expiresAt) {
            return back()->withErrors(['email' => 'Reset token has expired.']);
        }
        $route = '/';
        if ($type == 'admin') {
            $route = '/manage-admin/login';
            DB::table('users')->where('email', $request->input('email'))->update([
                'password' => bcrypt($request->input('password')),
            ]);
        } elseif ($type == 'program') {
            $custBrevoData = CustomreBrevoData::where(['email' => $request->input('email'), 'level' => 'admin'])->first();
            $program_id = $custBrevoData->program_id;
            // $Program = Program::where('id', $program_id)->first();
            $multiProgramLogin = ProgramMultiLogin::where(['customre_brevo_data_id' => $custBrevoData->id, 'program_id' => $program_id])->first();
            if(!$multiProgramLogin){
                $multiProgramLogin = new ProgramMultiLogin();
                $multiProgramLogin->customre_brevo_data_id = $custBrevoData->id;
                $multiProgramLogin->program_id = $program_id;
            }

            $multiProgramLogin->password = $request->input('password');
            $multiProgramLogin->save();

            // $Program->password = $request->input('password');
            // $Program->save();
            $route = '/manage-program/login';
            // DB::table('programs')->where('email', $request->input('email'))->update([
            //     'password' => ($request->input('password')),
            // ]);
        } elseif ($type == 'counsellor') {
            $route = '/counsellinglogin';
            DB::table('counselors')->where('email', $request->input('email'))->update([
                'password' => bcrypt($request->input('password')),
            ]);
        }
        DB::table('password_resets')->where('email', $request->input('email'))->delete();

        return redirect($route)->with('status', 'Password successfully reset.');
    }
    public function resetAppPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);
        $type = $request->type;
        $resetRecord = DB::table('password_resets')
            ->where('email', $request->input('email'))
            ->where('token', $request->input('token'))
            ->first();
        if (!$resetRecord) {
            return back()->withErrors(['email' => 'Invalid or expired reset token.']);
        }
        $expiresAt = now()->subMinutes(60);
        if ($resetRecord->created_at < $expiresAt) {
            return back()->withErrors(['email' => 'Reset token has expired.']);
        }
        $route = '/';

        DB::table('customers')->where('email', $request->input('email'))->update([
            'password' => bcrypt($request->input('password')),
        ]);
        DB::table('password_resets')->where('email', $request->input('email'))->delete();
        $factory = (new Factory)->withServiceAccount('public/mw-1' . DIRECTORY_SEPARATOR . 'firebase-credentials.json');
        $auth = $factory->createAuth();
        $user = $auth->getUserByEmail($request->input('email'));

        // Update the user's password
        $updatedUser = $auth->updateUser($user->uid, [
            'password' => $request->input('password'),
        ]);
        $auth->revokeRefreshTokens($user->uid);
        return back()->withErrors(['email' => 'Password Updated Successfully Go to the App and logged in Again.']);
    }
}
