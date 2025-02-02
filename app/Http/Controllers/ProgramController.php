<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Customer;
use App\Models\Program;
use App\Models\CustomreBrevoData;

use SendinBlue\Client\Model\CreateContact;
use SendinBlue\Client\Configuration;
use SendinBlue\Client\Api\ContactsApi;


use App\Models\CustomerRelatedProgram;
use App\Models\ProgramPlan;
use App\Models\Session;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Exception;
use Illuminate\Support\Facades\DB;
use PragmaRX\Google2FA\Google2FA;
use App\Models\ProgramDepartment;
use SendinBlue\Client\Model\RemoveContactFromList;
use SendinBlue\Client\ApiException;
class ProgramController extends Controller
{
    public function Login()
    {
        if (Auth::guard('programs')->check()) {
            return redirect()->route('program.dashboard');
        }
        return view('admin.programlogin');
    }


    public function logout()
    {
        // auth()->logout();
        Auth::guard('programs')->logout();
        session()->forget('loginUserName');
        return redirect('manage-program/login');
    }

    public function checkLogin(Request $request)
    {
        // Retrieve the program by the provided email
        if (!$request->has('email') || !$request->has('password')) {
            return back()->with('error', 'Email and Password is can not empty');
        }
        $custBrevoData = CustomreBrevoData::where(['email' => $request->email, 'level' => 'admin'])->first();
        if (!$custBrevoData) {
            return back()->with('error', 'Sorry Account Not Found');
        }
        $program = Program::where('id', $custBrevoData->program_id)->first();

        $is_trial_end = false;

        if ($program->program_type == 2) {
            $trial_end = $program->trial_expire;
            $today = Carbon::today();
            $targetDate = Carbon::create($trial_end);
            if ($today->greaterThanOrEqualTo($targetDate)) {
                $is_trial_end = true;
            } else {
                $is_trial_end = false;
        }
        }

        if($is_trial_end){
            return back()->with('error', 'Sorry Your trial period is end');
        }
         session()->forget('loginUserName');
        // Check if program exists and if the password matches (without hashing)
        if ($program && $custBrevoData?->MultiLoginProgram?->password === $request->password ) {
            if ($program->program_type == 0) {
                return back()->with('error', 'Your account is deactivated');
            }
            if ($program->is_2fa_enabled) {

                if ($request->session()->has('2fa_passed')) {
                    $request->session()->forget('2fa_passed');
                }
                $request->session()->put('2fa:user:id', $program->id);
                $request->session()->put('2fa:auth:attempt', true);
                $request->session()->put('2fa:auth:remember', $request->has('remember'));
                return redirect()->route('program.2fa');
            }
            if ($request->email === 'Yvette@mindwayapp.com') {
                // Redirect to a different view for this email
                return view('admin.session_dashboard_view');
            } else {
                // Redirect to the dashboard for other emails
                Auth::guard('programs')->login($program);
                session()->put('loginUserName', $custBrevoData->name ?? '');
                session()->forget('user_id');
                return redirect("/manage-program/view-dashboard");
            }
        }

        // Authentication failed...
        return back()->with('error', 'Password or email incorrect. If you’re still having trouble, reset your password');
    }
    public function decisionDashboardView()
    {
        return view('session_dashboard_view'); // Assuming 'decision_dashboard_view' is the name of your view file
    }


 public function adoptionRate($userId, $departId = null)
    {
        $data = CustomreBrevoData::where('program_id', $userId)->get();
        if($departId){
            $data = $data->where('department_id', $departId);
        }
        $totalEmployees = $data->count();
        $appUsers = $data->where('is_app_user', 1)->count();
        $counselingUsers = $data->where('is_counselling_user', 1)
            ->where('is_app_user', '!=', 1)
            ->count();
        $totalAdoption = $appUsers + $counselingUsers;
        $adoptionRate = $totalEmployees > 0 ? ($totalAdoption / $totalEmployees) * 100 : 0;
        return $adoptionRate;
    }
    public function dashboard()
    {
        $Program = Auth::guard('programs')->user();
        // dd($Program);
         list($leftDays, $is_trial) = $this->findTrialInfo($Program);
        //data Calculation


        $Program = Auth::guard('programs')->user();
        $userId = $Program->id;

        $allUsers = CustomreBrevoData::where('program_id', $userId)->get();
        $adoptedUsers = 0;
        foreach ($allUsers as $key => $u) {
            if($u->is_app_user == 1 || $u->is_counselling_user == 1){
                $adoptedUsers = $adoptedUsers +1;
                }
        }




        $programs = Program::where('id', $userId)->get();
        $newUserCount = Session::where('new_user', 'Yes')
            ->where('program_id', $userId)
            ->count();
        //echo "New User Count: " . $newUserCount . "<br>";

        $customerIds = CustomerRelatedProgram::where('program_id', $userId)->pluck('customer_id')->unique()->toArray();
        // Retrieve all customers based on the obtained IDs using the relationship
        $totalCustomers = CustomreBrevoData::whereIn('program_id', [$userId])->get();

        // $adoptionRate = $this->adoptionRate($userId);
        $count = count($Program->customers);
        //echo "App User: " . $count . "<br>";
        // echo $count;
        $customerCount = $Program->customers->count();
        $totalCount = $customerCount + $newUserCount; // Sum of customer count and new user count

        $sum = $count + $newUserCount;
        $totalCustomersCount = count($totalCustomers);


        //echo "EAP USER: " . $totalCustomersCount . "<br>";
        if ($totalCustomersCount != 0) {
            $customerPercentage = ($totalCustomersCount / $Program->max_lic) * 100;
            $customerPercentage = number_format($customerPercentage, 1); // Format to
        } else {
            $customerPercentage = 0; // or any default value you prefer
        }
        return view('mw-1.employeer.dashboard', get_defined_vars());
        return view('admin.view-program', compact('Program'));
        // dd($Program->customers);
        // if ($user = Auth::guard('programs')->user()) {
        //     $userId = $user->id;
        //     // Now $userId contains the authenticated user's ID
        //     dd($userId);
        // }
        //     $Program = Auth::guard('programs')->user();
        // $customers = $Program->customers;

        // // Dumping and dying the data for debugging
        // dd($customers);

        // // Alternatively, you can loop through the customers and display specific attributes
        // foreach ($customers as $customer) {
        //     echo "Name: " . $customer->name . "<br>";
        //     echo "Email: " . $customer->email . "<br>";
        //     echo "Session: " . $customer->pivot->session . "<br>";
        //     echo "<hr>";
        // }

    }

    public function RemoveReddemCode($customerId, $programId)
    {

        $customer = Customer::find($customerId);

        if ($customer) {
            // Detach the program from the customer
            $customer->programs()->detach($programId);
        }

        return back()->with('message', 'Record deleted successfully!');
    }
    public function save(Request $request)
    {
        // Validate the request data
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:customre_brevo_data', // Add unique validation for email
        ]);
        $userId = Auth::guard('programs')->id();
        $program = Program::where('id', $userId)->first();
        $customer = new CustomreBrevoData();
        $customer->name = $request->name;
        $customer->email = $request->email;
        $customer->program_id =  $userId;
        $customer->company_name = $program->company_name;
        $customer->max_session = $program->max_session;
        $customer->save();
        // Retrieve the newly created customer's ID
        $customerId = $customer->id;
        // Create a record in the customer_related_program table
        $customerRelatedProgram = new CustomerRelatedProgram();
        $customerRelatedProgram->customer_id = $customerId;
        $customerRelatedProgram->program_id = $userId; // Use the program ID obtained earlier
        $customerRelatedProgram->save();
        // Set up the SendinBlue API configuration
        $config = Configuration::getDefaultConfiguration()->setApiKey('api-key',env('BREVO_API_KEY'));

        // Create an instance of the ContactsApi
        $apiInstance = new ContactsApi(new Client(), $config); // Use the correct Client class

        // Prepare the data for creating the contact
        $createContact = new CreateContact([
            'email' => $request->email,
            'attributes' => (object) [
                'EMAIL' => $request->email,
                'FIRSTNAME' => $request->name,
                'CODEACCESS' => $program->code,
                'COMPANY' => $program->company_name,
                'MS' => $program->max_session,
                'LASTNAME' => ""
            ],
            'listIds' => [9], // Assuming you want to add the contact to list ID 1
        ]);


        try {
            // Make the request to create the contact
            $result = $apiInstance->createContact($createContact);

            // Process the response as needed
            // return response()->json($result);
            return back()->with('message', 'Record added successfully');
        } catch (\Illuminate\Database\QueryException $e) {
            // Catch the specific exception for duplicate entry constraint
            if ($e->getCode() == 23000) {
                return back()->with('error', 'User is already registered. Duplicate emails are not allowed.');
            } else {
                // Handle other query exceptions
                return back()->with('error', $e->getMessage());
            }
        } catch (Exception $e) {
            // Handle other exceptions
            return back()->with('error', $e->getMessage());
        }
    }
    public function uploadUsers(Request $request)
    {
        // Get names and emails from the request
        $names = $request->input('name');
        $emails = $request->input('email');

        // Create an array to store the data for Brevo
        $contactsData = [];
        $userId = Auth::guard('programs')->id();
        $program = Program::where('id', $userId)->first();
        // Initialize an array to track duplicate emails
        $duplicateEmails = [];

        // Loop through each name and email pair
        foreach ($names as $index => $name) {
            try {
                // Create a new customer record in the database
                $customer = new CustomreBrevoData();
                $customer->name = $name; // Use $name directly, no need for [$index]
                $customer->email = $emails[$index]; // Access email using $index
                $customer->company_name = $program->company_name;
                $customer->max_session = $program->max_session;
                $customer->program_id = $userId;
                $customer->save();

                // Retrieve the newly created customer's ID
                $customerId = $customer->id;

                // Create a record in the customer_related_program table
                $customerRelatedProgram = new CustomerRelatedProgram();
                $customerRelatedProgram->customer_id = $customerId;
                $customerRelatedProgram->program_id = $userId; // Use the program ID obtained earlier
                $customerRelatedProgram->save();

                // Add the contact data to the array for Brevo
                $contactsData[] = [
                    'email' => $emails[$index],
                    'attributes' => (object)[
                        'EMAIL' => $request->email,
                        'FIRSTNAME' => $name, // This line should fill in the 'FIRSTNAME' attribute
                        'LASTNAME' => "", // Make sure you fill this if applicable
                        'CODEACCESS' => $program->code,
                        'COMPANY' => $program->company_name,
                        'MS' => $program->max_session,
                    ],
                    'listIds' => [9] // Assuming you want to add all contacts to list ID 1
                ];
            } catch (\Illuminate\Database\QueryException $e) {
                // If the exception is due to duplicate entry error
                if ($e->errorInfo[1] === 1062) { // MySQL error code for duplicate entry
                    // Add the duplicate email to the list
                    $duplicateEmails[] = $emails[$index];
                } else {
                    // Handle other database exceptions if necessary
                    return back()->with('message', $e->getMessage());
                }
            }
        }

        // Set up the Brevo API configuration
        $config = Configuration::getDefaultConfiguration()->setApiKey('api-key',env('BREVO_API_KEY'));

        // Create an instance of the ContactsApi
        $apiInstance = new ContactsApi(new Client(), $config);

        try {
            // Make the request to create the contacts in Brevo
            foreach ($contactsData as $contactData) {
                $createContact = new CreateContact($contactData);
                $apiInstance->createContact($createContact);
            }

            // Return success response
            return back()->with('message', 'Records added successfully');
        } catch (Exception $e) {
            // Handle any exceptions that occur during the request
            return back()->with('message', $e->getMessage());
        }

        // If there were duplicate emails, notify the user
        if (!empty($duplicateEmails)) {
            $errorMessage = 'Given user already registered'; // Custom error message
            return back()->with('error', $errorMessage);
        }
    }



    public function processExcel(Request $request)
    {
        try {
            $file = $request->file('excelFile');

            // Read the Excel file and extract data
            $data = \Excel::toArray([], $file);

            // Initialize an empty array to store the extracted data
            $excelData = [];

            // Loop through the data and collect it into the array
            foreach ($data[0] as $row) {
                $excelData[] = [
                    'name' => $row[0], // Assuming the first column contains names
                    'email' => $row[1], // Assuming the second column contains emails
                ];
            }

            // Return the extracted data as a JSON response
            return response()->json(['success' => true, 'data' => $excelData]);
        } catch (\Exception $e) {
            // Handle exceptions and return error response
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }


    public function removeCustomer(Request $request)
    {
        $customerId = $request->input('customerId');
        $email = $request->input('email');
        try {
                
            $config = Configuration::getDefaultConfiguration()->setApiKey('api-key', env('BREVO_API_KEY'));
            $apiInstance = new ContactsApi(new Client(), $config);
            $contact = $apiInstance->getContactInfo($email);
            if (in_array(9, $contact->getListIds())) {
                // Remove the contact from list ID 9
                $contactIdentifiers = new RemoveContactFromList([
                    'emails' => [$email]
                ]);
                $apiInstance->removeContactFromList(9, $contactIdentifiers);
            } else {
                $contactIdentifiers = new RemoveContactFromList([
                    'emails' => [$email]
                ]);
                $apiInstance->removeContactFromList(11, $contactIdentifiers);
            }
        } catch (ApiException $e) {
        }
        catch (\Throwable $e) {
        }

        $customer = CustomreBrevoData::findOrFail($customerId);
        if($customer->level == 'admin')
        {
            return back()->with('error', 'Unable to remove the admin employee.');
        }
        if($customer->app_customer_id)
        {
            Customer::where('id',$customer->app_customer_id)->delete();
        }
        $customer->delete();
        return back()->with('message', 'Employee Deleted successfully');
    }

    public function viewEmployees()
    {
        $Program = Auth::guard('programs')->user();
        list($leftDays, $is_trial) = $this->findTrialInfo($Program);
        $userId = Auth::guard('programs')->id();
        $customers = CustomreBrevoData::where('program_id', $userId)->get();
        // $sessions = Session::where('program_id', $userId)->get();
        return view('mw-1.employeer.employees.manage', get_defined_vars());
    }

    public function addEmployees() {}

    public function setting()
    {
        $Program = Auth::guard('programs')->user();

        list($leftDays, $is_trial) = $this->findTrialInfo($Program);

        $plan = ProgramPlan::where('program_id', $Program->id)->first();
        $user = Auth::guard('programs')->user();
        $secret = null;
        $qrCodeUrl = null;
        if ($user->is_2fa_enabled) {
            $google2fa = new Google2FA();
            $secret = $user->google2fa_secret;
            $qrCodeUrl = $google2fa->getQRCodeUrl(
                config('app.name'),
                $user->email,
                $secret
            );
        }
        // dd($Program);
        return view('mw-1.employeer.settings.manage', get_defined_vars());
    }
    public function findTrialInfo($Program)
    {
        $is_trial = false;
        $leftDays  = 0;
        if ($Program->program_type == 2) {
            $is_trial = true;
            $trial_end = $Program->trial_expire; // Assuming trial_expire is a date string
            $today = Carbon::today();
            $targetDate = Carbon::create($trial_end);

            if ($today->greaterThanOrEqualTo($targetDate)) {
                $leftDays  = 0;
            } else {
                $leftDays  = $today->diffInDays($targetDate);
            }
        }
        return  [$leftDays, $is_trial];
    }




    public function saveSetting(Request $request)
    {
        $user = Auth::guard('programs')->user();
        $google2fa = new Google2FA();

        if ($request->has('enable_2fa')) {
            if(!$user->is_2fa_enabled)
            {
                $secret = $google2fa->generateSecretKey();
                $user->google2fa_secret = $secret;
                $user->is_2fa_enabled = true;
                $user->save();    
            }
            $qrCodeUrl = $google2fa->getQRCodeUrl(
                config('app.name'),
                $user->email,
                $user->google2fa_secret
            );

            return redirect()->route('setting')
                ->with(['message' => 'Two-factor authentication enabled!', 'qrCodeUrl' => $qrCodeUrl, 'secret' => $user->google2fa_secret]);
        } else {
            // Disable 2FA
            $user->google2fa_secret = null;
            $user->is_2fa_enabled = false;
            $user->save();

            return redirect()->route('setting')
                ->with(['success' => 'Two-factor authentication disabled!']);
        }
    }
    public function updateProgram(Request $request, $id)
    {

        $Program = Program::find($id);
        $imageName = '';
        if ($request->hasFile('logo')) {
            $image = $request->logo;
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $request->logo->storeAs('logo', $imageName);
            $Program->logo = $imageName;
        }
        $Program->company_name = $request->company_name;
        $Program->save();
        return back()->with('message', 'Information updated successfully!');
    }

    public function calculateGrowth($Program, $departId = null)
    {
        $userId = Auth::guard('programs')->user()->id;
    
        // Set the date range: from 12 months ago to the end of the current month
        $startDate = Carbon::now()->subMonths(11)->startOfMonth(); // 11 months ago (so we include current month)
        $endDate = Carbon::now()->endOfMonth(); // Current month end
    
        // Query with correct filtering
        $query = CustomreBrevoData::where('program_id', $userId)
            ->where(function ($query) {
                $query->where('is_app_user', 1)
                      ->orWhere('is_counselling_user', 1);
            })
            ->whereBetween('created_at', [$startDate, $endDate]);
    
        // Apply department filter if provided
        if ($departId) {
            $query->where('department_id', $departId);
        }
    
        // Fetch data once from the database
        $entries = $query->get();
    
        $growthData = [];
        for ($i = 0; $i < 12; $i++) { // Loop for 12 months including February
            $monthStart = $startDate->copy()->addMonths($i);
            $monthEnd = $monthStart->copy()->endOfMonth();
    
            // Count entries in this month
            $count = $entries->filter(function ($entry) use ($monthStart, $monthEnd) {
                return $entry->created_at >= $monthStart && $entry->created_at <= $monthEnd;
            })->count();
    
            $growthData[] = $count;
        }
    
        // Calculate cumulative values
        for ($i = 1; $i < count($growthData); $i++) {
            $growthData[$i] += $growthData[$i - 1];
        }
    
        // Generate month labels (e.g., "Mar", "Apr", ..., "Feb")
        $labels = [];
        for ($i = 0; $i < 12; $i++) {
            $labels[] = $startDate->copy()->addMonths($i)->format('M');
        }
    
        return [$growthData, $labels];
    }
    //Data of each month -- with out commulative -- not using at te moment
    public function calculateGrowth1($Program, $departId = null)
    {
        $userId = $Program->id;
        $months = collect();
        $counts = collect();
        $currentDate = Carbon::now();
        for ($i = 0; $i < 6; $i++) {
            $startOfMonth = $currentDate->copy()->subMonths($i)->startOfMonth();
            $endOfMonth = $currentDate->copy()->subMonths($i)->endOfMonth();
            $data = CustomreBrevoData::where('program_id', $userId)
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->get();

            if($departId){
                $data = $data->where('department_id', $departId);
            }

            $appUsers = $data->where('is_app_user', 1)->count();
            $counselingUsers = $data->where('is_counselling_user', 1)
                ->where('is_app_user', '!=', 1)
                ->count();
            $totalAdoption = $appUsers + $counselingUsers;
            // Add the count and formatted month to the collections
            $counts->prepend($totalAdoption);
            $months->prepend($startOfMonth->format('M y'));
        }

        return [$counts->toArray(), $months->toArray()];
    }

    public function calculateSessionGrowth($Program, $departId = null)
    {
        // Set the date range: from 6 months ago to the end of the current month
        $startDate = Carbon::now()->subMonths(6)->startOfMonth();
        $endDate = Carbon::now()->endOfMonth(); // Ensures we include the current month
    
        // Query with filters
        $query = Session::where('program_id', $Program->id)
            ->whereBetween('created_at', [$startDate, $endDate]);
    
        // Apply department filter if provided
        if ($departId) {
            $query->where('department_id', $departId);
        }
    
        // Fetch data once from the database
        $sessions = $query->orderBy('created_at')->get();
    
        $growthData = [];
        for ($i = 0; $i < 7; $i++) { // Loop for 7 months (including current)
            $monthStart = $startDate->copy()->addMonths($i);
            $monthEnd = $monthStart->copy()->endOfMonth();
    
            // Count entries in this month
            $count = $sessions->filter(function ($session) use ($monthStart, $monthEnd) {
                return $session->created_at >= $monthStart && $session->created_at <= $monthEnd;
            })->count();
    
            $growthData[] = $count;
        }
    
        // Calculate cumulative values
        for ($i = 1; $i < count($growthData); $i++) {
            $growthData[$i] += $growthData[$i - 1];
        }
    
        // Generate month labels (e.g., "Sep 23", "Oct 23", ..., "Feb 24")
        $labels = [];
        for ($i = 0; $i < 7; $i++) {
            $labels[] = $startDate->copy()->addMonths($i)->format('M y');
        }
    
        return [$growthData, $labels];
    }
    
    public function sessionReasons($Program, $departId = null)
    {
        $workRelatedReasons = [
            'Work Related',
            'Work Stress',
            'Workplace Conflicts',
            'Harassment/Bullying',
            'Performance Issues',
            'Organisational Change',
            'Burnout',
            'Other',
            'Person Related',
        ];

        // Calculate the date 6 months ago
        $sixMonthsAgo = Carbon::now()->subMonths(6);

        // Build the query with program filter and date range
        $query = DB::table('sessions')
            ->where('program_id', $Program->id)
            ->where('created_at', '>=', $sixMonthsAgo) // Assuming `created_at` stores the session date
            ->select('reason');

        // Apply department filter if provided
        if ($departId) {
            $query->where('department_id', $departId);
        }

        // Fetch filtered sessions
        $sessions = $query->get();

        // Initialize the counts
        $reasonCounts = array_fill_keys($workRelatedReasons, 0);

        // Process each session and count reasons
        foreach ($sessions as $session) {
            $reasons = explode(', ', $session->reason); // Split reasons by ', '
            foreach ($reasons as $reason) {
                if (in_array($reason, $workRelatedReasons)) {
                    $reasonCounts[$reason]++;
                }
            }
        }

        // Sort the reason counts in descending order
        arsort($reasonCounts);

        // Return sorted labels and data
        return [array_keys($reasonCounts), array_values($reasonCounts)];
    }

    public function sessionReasonsPercentage($Program, $departId = null)
    {
        $workRelatedReasons = [
            'Work Related',
            'Work Stress',
            'Workplace Conflicts',
            'Harassment/Bullying',
            'Performance Issues',
            'Organisational Change',
            'Burnout',
            'Other',
            'Person Related',
        ];

        // Calculate the date 6 months ago
        $sixMonthsAgo = Carbon::now()->subMonths(6);

        // Build the query with program filter and date range
        $query = DB::table('sessions')
            ->where('program_id', $Program->id)
            ->where('created_at', '>=', $sixMonthsAgo) // Assuming `created_at` stores the session date
            ->select('reason');

        // Apply department filter if provided
        if ($departId) {
            $query->where('department_id', $departId);
        }

        // Fetch filtered sessions
        $sessions = $query->get();

        // Initialize the counts
        $reasonCounts = array_fill_keys($workRelatedReasons, 0);

        // Process each session and count reasons
        foreach ($sessions as $session) {
            $reasons = explode(', ', $session->reason); // Split reasons by ', '
            foreach ($reasons as $reason) {
                if (in_array($reason, $workRelatedReasons)) {
                    $reasonCounts[$reason]++;
                }
            }
        }

        // Extract the counts
        $personRelatedCount = $reasonCounts['Person Related'];
        $otherReasonsCount = 0;

        foreach ($reasonCounts as $reason => $count) {
            if ($reason !== 'Other' && $reason !== 'Person Related') {
                $otherReasonsCount += $count;
            }
        }

        $totalCount = $personRelatedCount + $otherReasonsCount;

        // Calculate percentages
        $otherReasonsPercentage = $totalCount ? ($otherReasonsCount / $totalCount) * 100 : 0;
        $personRelatedPercentage = $totalCount ? ($personRelatedCount / $totalCount) * 100 : 0;

        return [
            'personRelatedCount' => $personRelatedCount,
            'workReasonsCount' => $otherReasonsCount,
            'workReasonsPercentage' => $otherReasonsPercentage,
            'personRelatedPercentage' => $personRelatedPercentage,
        ];
    }


    // public function viewAnalytics()
    // {

    //     $user = Auth::guard('programs')->user();

    //     $Program =  $user;
    //     $this->sessionReasonsPercentage($Program);


    //     $Program = Auth::guard('programs')->user();
    //     $userId = $Program->id;
    //     $programs = Program::where('id', $userId)->get();
    //     $newUserCount = Session::where('new_user', 'Yes')
    //         ->where('program_id', $userId)
    //         ->count();

    //     $totalSessions = Session::where('program_id', $userId)
    //         ->count();

    //   list($leftDays, $is_trial) = $this->findTrialInfo($Program);


    //     $customerIds = CustomerRelatedProgram::where('program_id', $userId)->pluck('customer_id')->unique()->toArray();

    //     // Retrieve all customers based on the obtained IDs using the relationship
    //     $totalCustomers = CustomreBrevoData::whereIn('id', $customerIds)->get();
    //     $allUsers = CustomreBrevoData::where('program_id', $userId)->get();
    //     $adoptedUsers = 0;
    //     foreach ($allUsers as $key => $u) {
    //         if($u->is_app_user == 1 || $u->is_counselling_user == 1){
    //             $adoptedUsers = $adoptedUsers +1;
    //       }

    //     }
    //     //Data calculate by zahid
    //     list($growthData, $labels) = $this->calculateGrowth($Program);
    //     list($growthDataSession, $labelsSession) =  $this->calculateSessionGrowth($Program);

    //     list($sessionReasonLabel, $sessionReasonData) = $this->sessionReasons($Program);

    //     $percentageData = $this->sessionReasonsPercentage($Program);
    //     $count = count($Program->customers);
    //     //echo "App User: " . $count . "<br>";
    //     // echo $count;
    //     // dd($Program->customers);
    //     $customerCount = $Program->customers->count();
    //     $totalCount = $customerCount + $newUserCount; // Sum of customer count and new user count

    //     $sum = $count + $newUserCount;
    //     $totalCustomersCount = count($totalCustomers);
    //     //echo "EAP USER: " . $totalCustomersCount . "<br>";

    //     $adoptionRate = $this->adoptionRate($userId);

    //     // if ($totalCustomersCount != 0) {
    //     //     $customerPercentage = ($totalCustomersCount / $Program->max_lic) * 100;
    //     //     $customerPercentage = number_format($customerPercentage, 1); // Format to one decimal place
    //     //     //echo "percentage: " .   $customerPercentage . "<br>";
    //     // } else {
    //     //     $customerPercentage = 0; // or any default value you prefer
    //     // }


    //     return view('mw-1.employeer.view-analytics', get_defined_vars());
    // }
    public function viewAnalytics(Request $request)
    {

        $departId = null;
        if($request->has('department')){
            $departId = $request->department;
        }
        $user = Auth::guard('programs')->user();

        $Program =  $user;
        $this->sessionReasonsPercentage($Program,$departId);


        $Program = Auth::guard('programs')->user();
        $userId = $Program->id;
        $programs = Program::where('id', $userId)->get();
        $newUserCount = Session::where('new_user', 'Yes')
        ->where('program_id', $userId);
        if ($departId) {
            $newUserCount->where('department_id', $departId);
        }
        $newUserCount = $newUserCount->count();

        $totalSessions = Session::where('program_id', $userId);

        if ($departId) {
            $totalSessions->where('department_id', $departId);
        }
        
        $totalSessions = $totalSessions->count();

       list($leftDays, $is_trial) = $this->findTrialInfo($Program);


        // $customerIds = CustomerRelatedProgram::where('program_id', $userId)->pluck('customer_id')->unique()->toArray();
        // $totalCustomers = CustomreBrevoData::whereIn('id', $customerIds)->get();

        $allUsers1 = CustomreBrevoData::where('program_id', $userId)
        ->where('created_at', '>=', Carbon::now()->subMonths(12))
        ;

        $allUsers = $allUsers1->get();
        $adoptedUsers = $allUsers1
        ->where(function ($query) {
            $query->where('is_app_user', 1)
                  ->orWhere('is_counselling_user', 1);
        })
        ;

        if($request->has('department')){
            $adoptedUsers = $adoptedUsers->where('department_id', $departId);
            // $allUsers = $allUsers->where('department_id', $departId);
        }
        $adoptedUsers=  $adoptedUsers->count();

        if($request->has('department')){
            $allUsers = $allUsers->where('department_id', $departId);
        }

        list($growthData, $labels) = $this->calculateGrowth($Program, $departId);
        list($growthDataSession, $labelsSession) =  $this->calculateSessionGrowth($Program,$departId);
        list($sessionReasonLabel, $sessionReasonData) = $this->sessionReasons($Program,$departId);

        $percentageData = $this->sessionReasonsPercentage($Program,$departId);
        $count = count($Program->customers);
        //echo "App User: " . $count . "<br>";
        // echo $count;
        // dd($Program->customers);
        $customerCount = $Program->customers->count();
        $totalCount = $customerCount + $newUserCount; // Sum of customer count and new user count
        $sum = $count + $newUserCount;
        // $totalCustomersCount = count($totalCustomers);
        $adoptionRate = $this->adoptionRate($userId, $departId);

        $departments = ProgramDepartment::where('program_id', $userId)->get();

        // $sum = $count + $newUserCount;
        // $totalCustomersCount = count($totalCustomers);
        //echo "EAP USER: " . $totalCustomersCount . "<br>";

        // $adoptionRate = $this->adoptionRate($userId);

        // if ($totalCustomersCount != 0) {
        //     $customerPercentage = ($totalCustomersCount / $Program->max_lic) * 100;
        //     $customerPercentage = number_format($customerPercentage, 1); // Format to one decimal place
        //     //echo "percentage: " .   $customerPercentage . "<br>";
        // } else {
        //     $customerPercentage = 0; // or any default value you prefer
        // }


        return view('mw-1.employeer.view-analytics', get_defined_vars());

    }
    public function saveName(Request $request)
    {

        if ($request->has('company_name')) {
            $Program = Auth::guard('programs')->user();

            $Program->company_name = $request->company_name;
            $Program->save();

            return response()->json(['status' => 'success', 'message' => 'Name saved successfully']);
        }
        return response()->json(['status' => 'error', 'message' => 'No Name Entered']);
    }


    public function saveProgramLogo(Request $request, $id)
    {
        $Program = Program::find($id);
        $imageName = '';
        if ($request->hasFile('logo')) {
            $image = $request->logo;
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $request->logo->storeAs('logo', $imageName);
            $Program->logo = $imageName;
        }
        $Program->company_name = $request->company_name;
        $Program->save();

        return response()->json(['status' => 'success', 'message' => 'Image Saved Successfully']);
    }
}
