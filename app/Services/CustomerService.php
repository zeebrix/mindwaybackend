<?php

namespace App\Services;

use App\Models\CustomreBrevoData;
use App\Models\Program;
use App\Models\Customer;
use App\Repositories\CustomersRepository;
use Illuminate\Support\Facades\Mail;
use SendinBlue\Client\Configuration;
use SendinBlue\Client\Api\ContactsApi;
use SendinBlue\Client\Model\UpdateContact;
use SendinBlue\Client\Model\CreateContact;



use SendinBlue\Client\ApiException;
use GuzzleHttp\Client;
use SendinBlue\Client\Model\RemoveContactFromList;



class CustomerService
{

    /**
     * @var CustomersRepository
     */
    protected $repository;

    public function __construct(CustomersRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param array $filters
     * @return mixed
     */
    public function getAll(array $filters = []): mixed
    {
        return $this->repository->getAll($filters);
    }

    /**
     * @param array $filters
     * @return mixed
     */

    public function store($request, array $modelValues = [])
    {
        $pushToBrevo = false;
        $input = $modelValues = $request;
        //Image Uploading
        $modelValues["verification_code"] = random_int(100000, 999999);
        $modelValues['password'] = \Hash::make($modelValues['password']);
        //  $modelValues['password'] = $modelValues['password'];
        \Arr::forget($modelValues, ["password_confirmation"]);

        $customer = $this->repository->store($modelValues);
        if ($modelValues['program_id'] && isset($modelValues['device_id'])) {
            $existingRecord = \DB::table('customers_programs')
                ->where('programs_id', $modelValues['program_id'])
                ->where('device', $modelValues['device_id'])
                ->where('customers_id', $customer->id)
                ->first();

            if (!$existingRecord) {
                // Record does not exist, so insert it
                \DB::table('customers_programs')->insert([
                    'programs_id' => $modelValues['program_id'],
                    'device' => $modelValues['device_id'],
                    'customers_id' => $customer->id,
                ]);
            }
        } else {
            try {
                // Send OTP email
                $otp = $modelValues["verification_code"];
                $email = $modelValues["email"];
                Mail::send('email.otp', ['otp' => $otp], function ($message) use ($email) {
                    $message->to($email)
                        ->subject('Your OTP Code');
                });
            } catch (\Exception $e) {
                return response()->json(['status' => false, 'message' => 'An error occurred while sending the email: ' . $e->getMessage()], 500);
            }
        }



        $customer2 = $this->repository->getOne($modelValues["email"], "email");
        $customer = $customer2->toArray();
        if ($modelValues['program_id']) {
           
                $customerbrevo = CustomreBrevoData::where('email', $modelValues['email'])->first();
                if(!$customerbrevo)
                {
                    $customerbrevo = new CustomreBrevoData();
                    $pushToBrevo = true;
                    $customerbrevo->level = 'member';
                }
                $program = Program::where('id', $modelValues['program_id'])->first();

            $customerbrevo->email = $modelValues['email'];
            $customerbrevo->name = $modelValues['name'];
            $customerbrevo->program_id = $modelValues['program_id'];
            $customerbrevo->max_session = $program->max_session;
            $customerbrevo->company_name = $program->company_name;
            $customerbrevo->is_app_user = true;
            $customerbrevo->app_customer_id = $customer['id'];
            $customerbrevo->save();

            $customer3 = Customer::where('id', $customer['id'])->first();
            $customer3->max_session = $program->max_session;
            $customer3->save();
            if ($pushToBrevo) {
                $config = Configuration::getDefaultConfiguration()->setApiKey('api-key', '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');

                // Create an instance of the ContactsApi
                $apiInstance = new ContactsApi(new Client(), $config); // Use the correct Client class

                // Prepare the data for creating the contact
                $createContact = new CreateContact([
                    'email' => $modelValues['email'],
                    'attributes' => (object) [
                        'EMAIL' => $modelValues['email'],
                        'FIRSTNAME' => $modelValues['name'],
                        'CODEACCESS' =>$program->code,
                        'COMPANY' => $program->company_name,
                        'MS' => $program->max_session,
                        'LASTNAME' => ""
                    ],
                    'listIds' => [11], // Assuming you want to add the contact to list ID 1
                ]);


                try {
                    // Make the request to create the contact
                    $result = $apiInstance->createContact($createContact);

                    // Process the response as needed
                    // return response()->json($result);
                    //return back()->with('message', 'Record added successfully');
                } catch (\Illuminate\Database\QueryException $e) {
                    // Catch the specific exception for duplicate entry constraint
                    if ($e->getCode() == 23000) {
                        //return back()->with('error', 'User is already registered. Duplicate emails are not allowed.');
                    } else {
                        
                        //return back()->with('error', $e->getMessage());
                    }
                } catch (Exception $e) {
                    
                    //return back()->with('error', $e->getMessage());
                }
            }
        }
        $customer["bearer_token"] = $customer["api_auth_token"] ?? NULL;
        return response()->json([
            'code' => 200,
            'status' => 'Success',
            'message' => 'Customer registered successfully.',
            'data' => $customer
        ], 200);
    }

    /**
     * @param array $filters
     * @return mixed
     */
    public function updateProfile($request)
    {
        $input = $modelValues = $request->all();
        //Image Uploading
        if ($request->hasFile('image')) {
            $modelValues["image"] = $request->file('image')->storeAs('users', request()->file('image')->getClientOriginalName());
        }

        $customerID = $request["customer_id"];
        \Arr::forget($modelValues, ["customer_id", "customer"]);
        if ($this->repository->update($modelValues, $customerID)) {
            $customer = $this->repository->getOne($customerID);
            $customer = $customer->toArray();
            $customer["bearer_token"] = $customer["api_auth_token"] ?? NULL;
            return response()->json([
                'code' => 200,
                'status' => 'Success',
                'message' => 'Profile updated successfully.',
                'data' => [$customer]
            ], 200);
        }

        return response()->json([
            'code' => 421,
            'status' => 'Error',
            'message' => 'Profile unable to update. Something went wrong.'
        ], 421);
    }

    public function verifySignup(array $modelValues = [])
    {
        $customer = $this->repository->getOne($modelValues["email"], "email");

        if ($customer && !$customer->verified_at && $customer->verification_code == $modelValues["verification_code"]) {
            $apiAuthToken = $this->repository->getUniqueValue(10, 'api_auth_token');
            $customer->verified_at = \Carbon\Carbon::now();
            $customer->status = TRUE;
            $customer->api_auth_token = $apiAuthToken;
            $customer->save();

            $customer = $customer->toArray();
            $customer["bearer_token"] = $customer["api_auth_token"] ?? NULL;
            return response()->json([
                'code' => 200,
                'status' => 'Success',
                'message' => 'Customer verified successfully.',
                'data' => [$customer]
            ], 200);
        }

        return response()->json([
            'code' => 421,
            'status' => 'Error',
            'message' => 'Phone number or verification code is invalid.'
        ], 421);
    }

    public function login(array $modelValues = [])
    {
        if (\Auth::guard('api')->attempt(['email' => request('email'), 'password' => request('password')])) {
            $apiAuthToken = $this->repository->getUniqueValue(10, 'api_auth_token');
            $user = \Auth::guard('api')->user();
            //            $token = $user->createToken('MyApp')->accessToken;
            $user->api_auth_token = $apiAuthToken;
            $user->save();
            $user = $user->toArray();
            $user["bearer_token"] = $user["api_auth_token"] ?? NULL;
            return response()->json([
                'code' => 200,
                'status' => 'Success',
                'message' => 'Login successfully.',
                'data' => [$user]
            ], 200);
        }

        return response()->json([
            'code' => 421,
            'status' => 'Error',
            'message' => 'Password or email incorrect. If you’re still having trouble, reset your password'
        ], 421);
    }

    public function logout(array $modelValues = [])
    {
        $update = array("api_auth_token" => NULL);
        if ($this->repository->update($update, $modelValues["customer_id"])) {
            return response()->json([
                'code' => 200,
                'status' => 'Success',
                'message' => 'Logout successfully.'
            ], 200);
        }

        return response()->json([
            'code' => 421,
            'status' => 'Error',
            'message' => 'Something went wrong.'
        ], 421);
    }

    public function forgetPassword(array $modelValues = [])
    {
        $customer = $this->repository->getOne($modelValues["phone"], "phone");

        if ($customer) {
            $customer->verified_at = NULL;
            $customer->verification_code = NULL;
            if (empty($customer->verification_code)) {
                $customer->verification_code = $this->repository->getUniqueValue(6, 'verification_code');
            }

            $customer->save();

            return response()->json([
                'code' => 200,
                'status' => 'Success',
                'message' => 'Verification code sent successfully.',
                'data' => [
                    "verification_code" => $customer->verification_code
                ]
            ], 200);
        }

        return response()->json([
            'code' => 421,
            'status' => 'Error',
            'message' => 'Unable to reset your password.'
        ], 421);
    }

    public function resetPassword(array $modelValues = [])
    {
        $customer = $this->repository->getOne($modelValues["phone"], "phone");

        if ($customer && !$customer->verified_at && $customer->verification_code == $modelValues["verification_code"]) {
            $customer->verified_at = \Carbon\Carbon::now();
            $customer->status = TRUE;
            $customer->password = \Hash::make($modelValues['password']);
            $customer->save();

            return response()->json([
                'code' => 200,
                'status' => 'Success',
                'message' => 'Password reset successfully.'
            ], 200);
        }

        return response()->json([
            'code' => 421,
            'status' => 'Error',
            'message' => 'Phone number or verification code is invalid.'
        ], 421);
    }
}
