<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\API\MobileApp\Auth\Customer\CustomerRegisterRequest;
use App\Models\Customers;
use App\Http\Requests\API\MobileApp\Auth\Customer\CustomerRegisterVerificationRequest;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\API\MobileApp\Auth\Customer\CustomerLoginRequest;
use App\Http\Requests\API\MobileApp\Auth\Customer\CustomerUpdateProfileRequest;
use App\Http\Requests\API\MobileApp\Auth\Customer\CustomerForgetPasswordRequest;
use App\Http\Requests\API\MobileApp\Auth\Customer\CustomerResetPasswordVerificationRequest;
use App\Http\Requests\API\MobileApp\Auth\Customer\ContactUsRequest;
use App\Http\Requests\API\MobileApp\Auth\Customer\CustomerGetNotifyRequest;
use App\Http\Requests\API\MobileApp\Auth\Customer\CustomerRegisterByEmailRequest;
use App\Models\ContactUs;
use App\Models\Customer;
use App\Models\Program;
use App\Models\CustomerFaq;
use App\Models\CustomreBrevoData;
use App\Models\Safety;
use App\Repositories\CustomersRepository;
use App\Services\CustomerService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
class CustomerController extends Controller
{

    protected $customerService;
    protected $repository;
    public function __construct(CustomerService $customerService,CustomersRepository $repository)
    {
        $this->customerService = $customerService;
        $this->repository = $repository;
    }
    public function passwordReset(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $email = $request->input('email');
        $customer = DB::table('customers')->where('email', $email)->first();
        if (!$customer) {
            return back()->withErrors(['email' => 'No account found with this email address.']);
        }
        $token = Str::random(64);
        DB::table('password_resets')->updateOrInsert(
            ['email' => $email],
            [
                'email' => $email,
                'token' => $token,
                'created_at' => now(),
            ]
        );
        // Generate the reset link
        $resetLink = url("/app-reset-password/{$token}?email={$email}");
        // Send the reset link to the user (email)
        // Here you can use Laravel Mail to send the reset link
        \Mail::to($email)->send(new \App\Mail\ResetPasswordMail($resetLink));
        return response()->json(['success'=>true]);
    }
    public function findMe(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);
        $is_resend = isset($request->is_resend) ? $request->is_resend : false;
        if(!$is_resend)
        {
            $user = Customer::Where('email',$request->email)->first();
            if($user)
            {
                return response()->json(['status' => false , 'message' => 'This email is already registered. Please try a different one or log in with this email instead.'], 400);
            }
        }
        $customer = NULL;
        if($request->verification_after == 'access_code'){
            $customer = Customer::where("email", $request->email)->first();
        }else{
            $customer = CustomreBrevoData::whereDoesntHave('customer')
            ->with('program.programDepartment')
            ->where("email", $request->email)
            ->first();
        }
        if ($customer) {
            $otp = random_int(100000, 999999);
            if($request->verification_after == 'access_code'){
                $customer->verification_code = $otp;
            }else{
                $customer->otp = $otp;
                $customer->otp_expiry = Carbon::now()->addMinutes(10);
            }
            $customer->save();
            try {
                // Send OTP email
                Mail::send('email.otp', ['otp' => $otp], function ($message) use ($customer) {
                    $message->to($customer->email)
                        ->subject('Your OTP Code');
                });
            } catch (\Exception $e) {
                return response()->json(['status' => false , 'message' => 'An error occurred while sending the email: ' . $e->getMessage()], 500);
            }
            return response()->json(['status' => true , 'data' => $customer , 'message' => 'OTP sent to the provided email.'], 200);
        } else {
            return response()->json(['status' => false , 'message' => 'No email found. Please try another email or use your access code below.'], 400);
        }
    }
    public function verifyOTP(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
        ]);
        $type = isset($request->type) ? $request->type : 'by_email';
        if($type == 'by_email')
        {
            $customer = CustomreBrevoData::with('program')->where("email", $request->email)->first();
            if ($customer && $customer->otp && Carbon::now()->lt($customer->otp_expiry)) {
                if ($customer->otp === $request->otp) {
                    return response()->json(['status' => true , 'data' => $customer , 'message' => 'OTP verified successfully.'], 200);
                } else {
                    return response()->json(['status' => false , 'message' => 'Invalid OTP.'], 400);
                }
            } else {
                return response()->json(['status' => false , 'message' => 'OTP not found or has expired.'], 400);
            }
        }
        else if($type == 'by_code')
        {
            $customer = Customer::where('email',$request->email)->first();
            if ($customer && $customer->verification_code == $request->otp) {
                $apiAuthToken = $this->repository->getUniqueValue(10, 'api_auth_token');
                $customer->verified_at = \Carbon\Carbon::now();
                $customer->status = TRUE;
                $customer->api_auth_token = $apiAuthToken;
                $customer->save();
                $token = $apiAuthToken ?? NULL;
                $useSanctum = request()->header('Use-Sanctum') === 'true';
                if($useSanctum)
                {
                    $token = $customer->createToken('auth_token')->plainTextToken;
                }
                $customer = $customer->toArray();
                $customer["bearer_token"] = $token ?? NULL ;
                
                return response()->json([
                    'code' => 200,
                    'status' => 'Success',
                    'message' => 'Customer verified successfully.',
                    'data' => [$customer]
                ], 200);
            }
            else
            {
                return response()->json(['status' => false , 'message' => 'OTP not found or has expired.'], 400);
            }

        }
        
    }

    public function register(CustomerRegisterRequest $request)
    {
        Log::info('Customer Register Request:', $request->all());
        return $this->customerService->store($request->all());
    }
    public function registerByEmail(CustomerRegisterByEmailRequest $request)
    {
        Log::info('Customer Register Request By Email:', $request->all());
        return $this->customerService->store($request->all());
    }
    public function verifySignup(CustomerRegisterVerificationRequest $request)
    {
        return $this->customerService->verifySignup($request->all());
    }

    public function login(CustomerLoginRequest $request)
    {
        return $this->customerService->login($request->all());
    }

    public function updateProfile(CustomerUpdateProfileRequest $request)
    {
        return $this->customerService->updateProfile($request);
    }

    public function logout(Request $request)
    {
        return $this->customerService->logout($request->all());
    }

    public function forgetPassword(CustomerForgetPasswordRequest $request)
    {
        return $this->customerService->forgetPassword($request->all());
    }

    public function resetPassword(CustomerResetPasswordVerificationRequest $request)
    {
        return $this->customerService->resetPassword($request->all());
    }

    public function getNotify(CustomerGetNotifyRequest $request)
    {
        if (isset($request['email']) && !empty($request['email'])) {
            $email_id = $request['email'];

            $getEmail = DB::table('customers')
                ->where('email', $email_id)
                ->update([
                    'notify_time' => $request['notify_time'],
                    'notify_day' => $request['notify_day'],
                ]);
            return response()->json(['code' => 200, 'status' => "success", 'message' => "Get notify time and day updated"]);
        } else {
            return response()->json(['code' => 401, 'status' => "failed", 'message' => "incomplete parameters email is required"]);
        }
    }
    public function updateGoalIdByEmail($email, $goalId)
    {
        $customer = Customer::where('email', $email)->first();

        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        $customer->goal_id = $goalId;
        $customer->save();

        return response()->json(['message' => 'Customer goal_id updated successfully'], 200);
    }

    public function getUser($id)
    {
        $user = Customer::find($id);
        return response()->json(['code' => 200, 'status' => "success", 'message' => "Your record fetched successfully!", 'data' => [$user]]);
    }

    // public function ReddemProgramCode(Request $request) {
    //     if(!$request->code || $request->code == '')
    //     {
    //         return response()->json(['code'=>401,'status'=>"failed",'message'=>"Code is Required"]);
    //     }
    //     if(!$request->customer_id || $request->customer_id == '')
    //     {
    //         return response()->json(['code'=>401,'status'=>"failed",'message'=>"customer_id is Required"]);
    //     }
    //     $program = Program::where('code', $request->code)->first();
    //     if (!$program->id || $program->id == '') {
    //         return response()->json(['code'=>401,'status'=>"failed",'message'=>"Code is not exits"]);
    //     } 

    //     $customer = Customer::find($request->customer_id);
    //     if($customer)
    //     {
    //         if ($customer->programs()->where('programs.id', $program->id)->exists()) {
    //             return response()->json(['code'=>401,'status'=>"failed",'message'=>"Customer has Already Reddemed Code!"]);
    //         } else {
    //             $customer->programs()->attach($program->id);
    //             return response()->json(['code'=>200,'status'=>"success",'message'=>"Code Reddem successfully!",'data'=>$customer]);
    //         }

    //     }
    //     else
    //     {
    //         return response()->json(['code'=>401,'status'=>"failed",'message'=>"Customer is not exits"]);
    //     }

    // }
    public function getCodeInformation(Request $request)
    {
        $program = Program::where('code', $request->code)->with('programDepartment')->first();
        if (!$program) {
            return response()->json(['code' => 401, 'status' => "failed", 'message' => "Code is not exits"]);
        } 
        return response()->json(['code' => 200, 'result' => $program, 'status' => "success", 'message' => "Code Reddem successfully!"]);
    }
    public function ReddemProgramCode(Request $request)
    {
        if (!$request->code || $request->code == '') {
            return response()->json(['code' => 401, 'status' => "failed", 'message' => "Code is Required"]);
        }
        if (!$request->device_id || $request->device_id == '') {
            return response()->json(['code' => 401, 'status' => "failed", 'message' => "device_id is Required"]);
        }

        $program = Program::where('code', $request->code)->with('programDepartment')->first();
        if (!$program) {
            return response()->json(['code' => 401, 'status' => "failed", 'message' => "Code is not exits"]);
        }
        if(isset($request->customer_id)){
            $user = Customer::where("id",$request->customer_id)->first();
       
            $brevoCustomer = CustomreBrevoData::where('app_customer_id',$user->id)->first();
            if($brevoCustomer)
            {
                        $brevoCustomer->program_id = $program->id;
                        $brevoCustomer->company_name = $program->company_name;
                        $brevoCustomer->app_customer_id = $user->id;
                        $brevoCustomer->is_app_user = true;
                        $brevoCustomer->max_session = $program->max_session??0;
                        $brevoCustomer->save();
                        $user->max_session = $program->max_session??0;
                        $user->save();
            }
            else
            {
                try {
                    $brevo = new CustomreBrevoData();
                    $brevo->name = $user->name;
                    $brevo->email = $user->email;
                    $brevo->program_id = $program->id;
                    $brevo->company_name = $program->company_name??'';
                    $brevo->max_session = $program->max_session??0;
                    $brevo->is_app_user = true;
                    $brevo->app_customer_id = $user->id;
                    $brevo->save();
                } catch (\Throwable $th) {
                    $brevo = new CustomreBrevoData();
                $brevo->name = $user->name;
                $brevo->email = 'dummyemail' . time(). '@example.com';
                $brevo->program_id = $program->id;
                $brevo->company_name = $program->company_name??'';
                $brevo->max_session = $program->max_session??0;
                $brevo->is_app_user = true;
                $brevo->app_customer_id = $user->id;
                $brevo->save();
                }
            }
        }
        
        $existingRecord = DB::table('customers_programs')
            ->where('programs_id', $program->id)
            ->where('device', $request->device_id)
            ->first();

        if (!$existingRecord) {
            // Record does not exist, so insert it
            DB::table('customers_programs')->insert([
                'programs_id' => $program->id,
                'device' => $request->device_id,
            ]);
            return response()->json(['code' => 200, 'result' => $program, 'status' => "success", 'message' => "Code Reddem successfully!"]);
        } else {
            return response()->json(['code' => 401, 'status' => "failed", 'message' => "Device has Already Reddemed Code!"]);
        }
    }

    public function updatedeviceid(Request $request)
    {
        if (!$request->customer_id || $request->customer_id == '') {
            return response()->json(['code' => 401, 'status' => "failed", 'message' => "customer_id is Required"]);
        }
        if (!$request->device_id || $request->device_id == '') {
            return response()->json(['code' => 401, 'status' => "failed", 'message' => "device_id is Required"]);
        }

        DB::table('customers_programs')
            ->where('device', $request->device_id)
            ->update(['customers_id' => $request->customer_id]);
        $user = Customer::where("id",$request->customer_id)->with('Program')->first();
        $brevoCustomer = CustomreBrevoData::where('app_customer_id',$user->id)->first();
        if($brevoCustomer)
        {
                    $brevoCustomer->program_id = $user?->single_program?->id;
                    $brevoCustomer->app_customer_id = $user->id;
                    $brevoCustomer->company_name = $user?->single_program?->company_name;
                    $brevoCustomer->is_app_user = true;
                    $brevoCustomer->max_session = $user?->single_program?->max_session;
                    $brevoCustomer->save();
                    $user->max_session = $user?->single_program?->max_session;
                    $user->save();
        }
        else
        {
            $brevo = new CustomreBrevoData();
            $brevo->name = $user->name;
            $brevo->email = 'dummyemail' . time(). '@example.com';
            $brevo->program_id = $user?->single_program?->id;
            $brevo->company_name = $user?->single_program?->company_name;
            $brevo->max_session = $user?->single_program?->max_session;
            $brevo->is_app_user = true;
            $brevo->app_customer_id = $user->id;
            $brevo->save();
        }
        return response()->json(['code' => 200, 'status' => "success", 'message' => "Updated successfully!"]);
    }

    public function getuserprograms(Request $request)
    {

        if (!$request->device_id || $request->device_id == '') {
            return response()->json(['code' => 401, 'status' => "failed", 'message' => "device_id is Required"]);
        }
        $relationship = DB::table('customers_programs')
            ->where('device', $request->device_id)->first();

        if ($relationship) {
            $customer = Customer::with('Program')->where('id', $relationship->customers_id)
                ->first();


            return response()->json(['code' => 200, 'status' => "success", 'message' => "Your record fetched successfully!", 'data' => [$customer]]);
        }

        return response()->json(['code' => 200, 'status' => "success", 'message' => "Your record fetched successfully!", 'data' => []]);
    }
}
