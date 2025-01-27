<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\CustomreBrevoData;
use App\Models\Program;
use App\Models\ProgramMultiLogin;
use Google\Service\CloudFunctions\SetupFunctionUpgradeConfigRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RegistrationController extends Controller
{

    public function programSignup()
    {
        session()->forget('otp');
        session()->forget('admin_name');
        session()->forget('admin_email');
        return view('mw-1.employeer.signup.signup');
    }

    public function submitSignUp(Request $request)
    {
        if (!$request->email) {
            return back()->with('error', 'Email is Required');
        }

        $adminUser = CustomreBrevoData::where(['email' => $request->email, 'level' => 'admin'])->first();

        if (!$adminUser) {
            return back()->with('error', 'No email found. Please try another email. If the issue persists, contact support@mindwayeap.com.au as you may not be added to the admin portal or have admin privileges.');
        }

        $recipient = $request->email;
        $randomNumber = rand(100000, 999999);
        $adminName = $adminUser->name;

        session()->put('otp', $randomNumber);
        session()->put('admin_name', $adminName);
        session()->put('admin_email',  $recipient);

        $this->OTPEmailSend($adminName, $randomNumber, $recipient);

        $route = 'manage-program/otp';
        return redirect($route)->with('status', 'OTP sent to your Email please - Please check your email');
    }

    public function OTPEmailSend($adminName, $randomNumber, $recipient)
    {
        $subject = 'Welcome to Mindway EAP';
        $template = 'emails.otp-program';
        $data = [
            'full_name' => $adminName,
            'otp' => $randomNumber,
        ];
        sendDynamicEmailFromTemplate($recipient, $subject, $template, $data);
    }


    public function programOtp()
    {
        return view('mw-1.employeer.signup.verify-otp');
    }


    public function verifyOtp(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'otp' => 'required|digits:6',
        ]);
        $otp = $request->input('otp');

        if ($otp == session('otp')) {

            $route = 'manage-program/setpassword';
            return redirect($route)->with('status', 'Please Set your password');

            // $programId = $adminUser->program_id;

            // Example OTP check
            return response()->json(['message' => 'OTP verified successfully!']);
        } else {
            return back()->with('error', 'Invalid OTP. Please try again');
        }
    }

    public function setPassword()
    {
        return view('mw-1.employeer.signup.program-setpassword');
    }

    public function setPassAndAccs(Request $request)
    {
        if (!$request->password) {
            return back()->with('error', 'please enter password');
        }

        $adminUser = CustomreBrevoData::where(['email' => session('admin_email'), 'level' => 'admin'])->first();

        if (!$adminUser) {
            return redirect('program-signup');
        }


        $programId = $adminUser->program_id;
        $Program = Program::find($programId);

        if (!$Program) {
            return back()->with('error', 'Program does not exist');
        }

        $multiLogin = ProgramMultiLogin::where(['program_id' => $Program->id, 'customre_brevo_data_id' => $adminUser->id])->first();
        if(!$multiLogin){
            $multiLogin = new ProgramMultiLogin();
            $multiLogin->program_id =$Program->id;
            $multiLogin->customre_brevo_data_id = $adminUser->id;
        }
        $multiLogin->password = $request->password;
        $multiLogin->save();
        // $Program->password = $request->password;
        // $Program->save();
        Auth::guard('programs')->login($Program);
        session()->put('loginUserName', $adminUser->name ?? '');
        session()->forget('user_id');
        session()->forget('otp');
        session()->forget('admin_name');
        session()->forget('admin_email');

        return redirect("/manage-program/view-dashboard");
    }


    public function resendOTP()
    {

        $email = session('admin_email');
        $adminUser = CustomreBrevoData::where(['email' => $email, 'level' => 'admin'])->first();

        if (!$adminUser) {
            return redirect('program-signup');
        }
        session()->forget('otp');
        $randomNumber = rand(100000, 999999);
        session()->put('otp', $randomNumber);
        $this->OTPEmailSend($adminUser->name, $randomNumber, $email);

        return back()->with('error', 'New OTP Send, Please check your Email');


    }
}
