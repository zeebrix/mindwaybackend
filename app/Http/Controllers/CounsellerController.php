<?php

namespace App\Http\Controllers;

use App\Models\Availability;
use App\Models\Booking;
use Illuminate\Http\Request;
use App\Models\Program;
use App\Models\Session;
use App\Models\CounsellingSession;
use App\Models\Counselor;
use App\Models\Customer;
use App\Models\CustomreBrevoData;
use App\Notifications\BookingCancellation;
use App\Services\SlotGenerationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use PragmaRX\Google2FA\Google2FA;
use Yajra\DataTables\Facades\DataTables;

class CounsellerController extends Controller
{
    public function findRecommendedCounselor(Request $request)
    {
        $customer = Customer::find($request->customer_id);
        $gender = $customer->gender ?? 'Male';
        $day = $request->input('day', 'Monday'); // Default to Monday
        $timezone = $request->input('timezone'); // Customer's timezone

        $daysChecked = [];
        $currentDay = $day;

        while (count($daysChecked) < 7) {
            $daysChecked[] = $currentDay;

            // Get counselors by gender
            $counselors = Counselor::where('gender', $gender)->get();

            // Filter available counselors
            $availableCounselors = $counselors->map(function ($counselor) use ($currentDay, $timezone) {
                $availability = $counselor->availabilities()
                    ->where('day', $currentDay)
                    ->where('available', true)
                    ->first();

                if (!$availability) return null;

                // Convert times to the customer's timezone
                $startTime = Carbon::createFromFormat('H:i:s', $availability->start_time, $counselor->timezone)
                    ->setTimezone($timezone);
                $currentCustomerTime = Carbon::now($timezone);
                $hoursUntil = $currentCustomerTime->diffInHours($startTime, false);

                return [
                    'id' => $counselor->id,
                    'name' => $counselor->name,
                    'email' => $counselor->email,
                    'gender' => $counselor->gender,
                    'timezone' => $counselor->timezone,
                    'next_availability' => [
                        'start_time' => $startTime->toTimeString(),
                        'hours_until' => $hoursUntil,
                    ],
                ];
            })->filter();

            // Sort by the earliest availability
            $sortedCounselors = $availableCounselors->sortBy('next_availability.hours_until');

            if ($sortedCounselors->isNotEmpty()) {
                $recommendedCounselor = $sortedCounselors->first();

                return response()->json([
                    'recommended_counselor' => $recommendedCounselor,
                    'all_counselors' => $sortedCounselors->values(),
                ]);
            }

            // Move to the next day if no counselors are available
            $currentDay = Carbon::parse($currentDay)->addDay()->format('l');
        }

        return response()->json([
            'message' => 'No counselors available for the selected criteria.',
            'recommended_counselor' => null,
            'all_counselors' => [],
        ]);
    }

    public function getCounselorCalendar(Request $request, $counselorId)
    {
        // Get the counselor with their availability and counseling sessions
        $counselor = Counselor::with(['availabilities', 'CounsellingSession'])->findOrFail($counselorId);

        // Specify the month (you can modify this to be dynamic)
        $startDate = Carbon::now()->startOfMonth(); // Example: December 1
        $endDate = Carbon::now()->endOfMonth(); // Example: December 31

        // Initialize calendar structure
        $calendar = [];
        $dayMap = [
            'Monday'    => 0,
            'Tuesday'   => 1,
            'Wednesday' => 2,
            'Thursday'  => 3,
            'Friday'    => 4,
            'Saturday'  => 5,
            'Sunday'    => 6
        ];
        // Iterate over each day in the month
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $dayOfWeek = $date->format('l'); // e.g., Monday
            $dayOfWeek = $dayMap[$dayOfWeek];
            $formattedDate = $date->toDateString(); // e.g., 2024-12-01

            // Check availability for the day
            $availability = $counselor->availabilities
                ->where('day', $dayOfWeek)
                ->where('available', true)
                ->first();

            // Get counseling sessions (bookings) for the day using `session_date`
            $sessions = $counselor->CounsellingSession->filter(function ($session) use ($formattedDate) {
                return $session->session_date === $formattedDate;
            });

            // Add the day's data to the calendar
            $calendar[] = [
                'date' => $formattedDate,
                'available' => $availability ? true : false,
                'sessions' => collect($sessions->all())->values(),
            ];
        }

        // Return the calendar as JSON
        return response()->json([
            'counselor_id' => $counselor->id,
            'calendar' => $calendar,
        ]);
    }


    public function seecounselling()
    {
        if(Auth::guard('counselor')->user())
        {
            return redirect()->route('counseller.dashboard');
        }
        return view('admin.counsellerlogin');
    }
    public function checkLoginCounseler(Request $request)
    {
        $Counselor = Counselor::where('email', $request->email)->first();
        if ($Counselor && Hash::check($request->password, $Counselor->password)) {
            if ($Counselor->uses_two_factor_auth) {

                if ($request->session()->has('2fa_passed')) {
                    $request->session()->forget('2fa_passed');
                }
                $request->session()->put('2fa:user:id', $Counselor->id);
                $request->session()->put('2fa:auth:attempt', true);
                $request->session()->put('2fa:auth:remember', $request->has('remember'));
                return redirect()->route('counselor.2fa');
            }

            // Password matches, log the user in and redirect to the dashboard
            if (Auth::guard('programs')->check()) {
                Auth::guard('programs')->logout();
            }
            session()->forget('user_id');
            Auth::guard('counselor')->login($Counselor);
            return redirect()->route('counseller.dashboard');
        } else {
            return back()->with('error', 'Wrong Login Details');
        }
    }
    public function counselorDashboard()
    {
        $Counselor = Auth::guard('counselor')->user();
        $counsellorId = $Counselor->id??null;
        if(!$counsellorId)
        {
            return redirect()->route('counseller.login');
        }
        if (Auth::guard('programs')->check()) {
            Auth::guard('programs')->logout();
        }
        $customers = CustomreBrevoData::all();
        $upcomingBookings = Booking::with(['user','counselor', 'slot'])
        ->where('counselor_id', $counsellorId)
        ->where('status', 'confirmed')
        ->whereHas('slot', function ($query) {
            $query->where('start_time', '>', now()->subHours(24));
        })
        ->orderBy('created_at', 'desc')
        ->paginate(10);
        $timezone = $Counselor->timezone??'UTC';
        // $timezone = 'Europe/London';
        return view('mw-1.counseller.dashboard', get_defined_vars());

    }
    public function logout()
    {
        Auth::guard('counselor')->logout();
        return redirect()->route('counseller.login');
    }

    public function index()
    {
        $user = Auth::guard('counselor')->user();
        $user_id = $user->id;
        $customers = CustomreBrevoData::all();
        return view('mw-1.counseller.sessions.manage', get_defined_vars());
        return view('admin.session_dashboard_view')->with(['user_id' => $user_id]);
    }
    
    public function getCounsellerSesions(Request $request)
    {
    if ($request->ajax()) {
        $searchText = $request->input('search.value'); // Get the search term from DataTables

        $customers = CustomreBrevoData::query();

        // Apply search filter if a search term is provided
        if (!empty($searchText)) {
            $customers->where(function ($query) use ($searchText) {
                $query->where('name', 'like', '%' . $searchText . '%') // Search by name
                      ->orWhere('email', 'like', '%' . $searchText . '%') // Search by email
                      ->orWhere('company_name', 'like', '%' . $searchText . '%'); // Search by company name
            });
        }

        return DataTables::of($customers)
            ->addColumn('count', function ($customer) use (&$count) {
                // Add row number (count)
                return '<h6 class="fw-normal mb-0">' . ++$count . '</h6>';
            })
            ->addColumn('name_email', function ($customer) {
                // Combine name and email into a single column
                return '
                    <h6 class="fw-semibold mb-1"><b>' . $customer->name . '</b></h6>
                    <p class="mb-0 fw-semibold">' . $customer->email . '</p>';
            })
            ->addColumn('company_name', function ($customer) {
                // Display company name
                return '<h6 class="mb-0 fw-bold"><b>' . $customer->company_name . '</b></h6>';
            })
            ->addColumn('max_session', function ($customer) {
                // Display max session count
                return '<h6 class="mb-0 fw-semibold"><b>' . $customer->max_session . '</b></h6>';
            })
            ->addColumn('action', function ($customer) {
                // Add the "Log" button with data attributes
                return '
                    <button type="button" class="btn btn-primary add-session-btn mindway-btn" 
                        style="background-color: #688EDC !important; color: #F7F7F7 !important" 
                        data-bs-toggle="modal" data-bs-target="#addSessionModal" 
                        data-id="' . $customer->id . '" 
                        data-name="' . $customer->company_name . '" 
                        data-program_id="' . $customer->program_id . '" 
                        data-customer_name="' . $customer->name . '">
                        Log
                    </button>';
            })
            ->rawColumns(['count', 'name_email', 'company_name', 'max_session', 'action']) // Ensure HTML is rendered
            ->make(true);
    }
}


// public function getCounsellerSesions(Request $request)
// {
//     if ($request->ajax()) {
//         $customers = CustomreBrevoData::query(); // Fetches all columns

//         return DataTables::of($customers)
//             ->addColumn('count', function ($customer) use (&$count) {
//                 // Add row number (count)
//                 return '<h6 class="fw-normal mb-0">' . ++$count . '</h6>';
//             })
//             ->addColumn('name_email', function ($customer) {
//                 // Combine name and email into a single column
//                 return '
//                     <h6 class="fw-semibold mb-1"><b>' . $customer->name . '</b></h6>
//                     <p class="mb-0 fw-semibold">' . $customer->email . '</p>';
//             })
//             ->addColumn('company_name', function ($customer) {
//                 // Display company name
//                 return '<h6 class="mb-0 fw-bold"><b>' . $customer->company_name . '</b></h6>';
//             })
//             ->addColumn('max_session', function ($customer) {
//                 // Display max session count
//                 return '<h6 class="mb-0 fw-semibold"><b>' . $customer->max_session . '</b></h6>';
//             })
//             ->addColumn('action', function ($customer) {
//                 // Add the "Log" button with data attributes
//                 return '
//                     <button type="button" class="btn btn-primary add-session-btn mindway-btn" 
//                         style="background-color: #688EDC !important; color: #F7F7F7 !important" 
//                         data-bs-toggle="modal" data-bs-target="#addSessionModal" 
//                         data-id="' . $customer->id . '" 
//                         data-name="' . $customer->company_name . '" 
//                         data-program_id="' . $customer->program_id . '" 
//                         data-customer_name="' . $customer->name . '">
//                         Log
//                     </button>';
//             })
//             ->rawColumns(['count', 'name_email', 'company_name', 'max_session', 'action']) // Ensure HTML is rendered
//             ->make(true);
//     }
// }


    public function store(Request $request)
    {
        // Initialize the reason array
        $reasons = [];
        // Check if checkboxes are present and append their values to the reason array
        if ($request->has('work_related')) $reasons[] = $request->input('work_related');
        if ($request->has('work_stress')) $reasons[] = $request->input('work_stress');
        if ($request->has('workplace_conflicts')) $reasons[] = $request->input('workplace_conflicts');
        if ($request->has('harassment_bullying')) $reasons[] = $request->input('harassment_bullying');
        if ($request->has('performance_issues')) $reasons[] = $request->input('performance_issues');
        if ($request->has('organisational_change')) $reasons[] = $request->input('organisational_change');
        if ($request->has('burnout')) $reasons[] = $request->input('burnout');
        if ($request->has('other')) $reasons[] = $request->input('other');
        if ($request->has('other_reason')) $reasons[] = $request->input('other_reason');
        if ($request->has('person_related')) $reasons[] = $request->input('person_related');

        // Concatenate reasons into a single string

        // Get new_user value
        // Concatenate reasons into a single string
        $reasonString = implode(', ', $reasons);
        $reasonStrings = rtrim($reasonString, ', ');

        // Get new_user value
        $newUser = $request->input('new_user', 'No');
        // Save to database
        
        // Fetch data from Session model using program_id
        
        try
        {
             $upcomingBookings = Booking::where('counselor_id', $request->counselor_id )
            ->where('slot_id', $request->slot_id)
            ->first();
           
            if($upcomingBookings)
            {
                $upcomingBookings->status = 'completed';
                $upcomingBookings->save();
            }
        }
        catch (\Throwable $th) {
            
        }
        
        if($request->type == 'upcomingSession')
        {
            $sessionData = CustomreBrevoData::where('app_customer_id', $request->customerId)->first();
        }
        else
        {
            $sessionData = CustomreBrevoData::where('id', $request->customerId)->first();
        }
        if ($sessionData)
        {
            $sessionData->max_session = $sessionData->max_session - 1;
            $sessionData->is_counselling_user = true;
            $sessionData->save();
            if($sessionData->app_customer_id)
            {
                $customer3 = Customer::where('id',$sessionData->app_customer_id)->first();
                if($customer3)
                {
                    $customer3->max_session = $sessionData->max_session;
                    $customer3->save();
                }
            }
                
       Session::create([
            'session_date' => $request->sessionDate,
            'session_type' => $request->sessionType,
            'reason' => $reasonStrings,
            'new_user' => $newUser,
            'program_id' => $request->programId??$sessionData->program_id,
            'department_id' => $sessionData?->department_id??null,
            'counselor_id' => $request->counselor_id ?? null
        ]);
            // Save to CounsellingSession table
            CounsellingSession::create([
                'session_date' => $request->sessionDate,
                'session_type' => $request->sessionType,
                'reason' => $reasonStrings,
                ' ' => $newUser,
                'program_id' => $request->programId??$sessionData->program_id,
                'company_name' => $sessionData->company_name,
                'name' => $sessionData->name,
                'email' => $sessionData->email,
                'department_id' => $sessionData?->department_id??null,
                'counselor_id' => $request->counselor_id ?? null,
                'max_session' => $sessionData->max_session, // Assuming you want to store this as well
            ]);

            // Redirect or return response
            return redirect()->route('counsellersesion.index')->with('message', 'Session data saved successfully for '. $sessionData->name);
        } else {
            // Handle the case where session data is not found
            return redirect()->route('counsellersesion.index')->with('error', 'Program ID not found.');
        }
    }
    public function SaveCounselorIntroVideo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'intro_video' => 'required|mimetypes:video/mp4,video/mov,video/avi,video/webm|max:10240', // Max 10MB
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422); // 422 Unprocessable Entity
        }
        $Counselor = Counselor::where('id', $request->counselorId)->first();
        $imageName = '';
        if ($request->hasFile('intro_video')) {
            $image = $request->file('intro_video'); // Use `file()` for clarity
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('public/Intro', $imageName); // Saves to storage/logo
            $Counselor->intro_file = $imageName;
        }
        $Counselor->save();
        return response()->json(['status' => 'success', 'message' => 'File Saved Successfully']);
   
    }
   public function saveCounsellorLogo(Request $request)
    {
       $request->validate([
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        $Counselor = Auth::guard('counselor')->user();
        $imageName = '';
        if ($request->hasFile('logo')) {
            $image = $request->file('logo'); // Use `file()` for clarity
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('logo', $imageName); // Saves to storage/logo
            $Counselor->avatar = $imageName;
        }
        $Counselor->save();
        // return back()->with(['message' => "Information Saved Successfully"]);

        return response()->json(['status' => 'success', 'message' => 'Image Saved Successfully']);
    }


    public function counsellerhome()
    {
        $user = Auth::guard('counselor')->user();
        $counsellor_id = $user->id;
        if ($counsellor_id) {
            $Counselor = Counselor::where('id', $counsellor_id)->first();

            if ($Counselor) {
                $customers = CustomreBrevoData::all();
                return view('mw-1.counseller.dashboard', get_defined_vars());
            }
        }

        return redirect()->route('counseller.login');
    }

    public function counsellerAvailability()
    {
        $user = Auth::guard('counselor')->user();
        $user_id = $user->id;
        $counselor = Counselor::where('id', $user_id)->firstOrFail();

        
        $availability = $counselor->availabilities()->get();
        $availabilityData = [];
        $currentTimezone = $counselor->timezone; // Replace with actual logic to get the user's timezone
        $timezones = $this->timezones();
        $counselorId = $counselor->id;
        foreach ($availability as $schedule) {
             $startTimeInCounselorTimezone = Carbon::parse($schedule->start_time)->setTimezone($currentTimezone);
            $endTimeInCounselorTimezone = Carbon::parse($schedule->end_time)->setTimezone($currentTimezone);

            $availabilityData[$schedule->day] = [
                'available' => $schedule->available,
                'start_time' => $startTimeInCounselorTimezone->format('H:i'),
                'end_time' => $endTimeInCounselorTimezone->format('H:i'),
            ];
        }
        // Get current timezone (you may need to store this in the user's settings)
       
        return view('mw-1.counseller.counsellor-availability', compact('availabilityData', 'currentTimezone', 'timezones','counselorId'));
    }
    public function fetchCounsellorAvailability(Request $request)
    {
        $availabilities = Availability::where('counselor_id',$request->counselorId)->get();
        
        $availability = [];
        $currentTimezone = (count($availabilities) > 0 ) ? $availabilities[0]?->counselor?->timezone : 'UTC';
        if(!empty($availabilities))
        {
           foreach($availabilities as $data)
           {
                $startTimeInCounselorTimezone = Carbon::parse($data->start_time)->setTimezone($currentTimezone);
                $endTimeInCounselorTimezone = Carbon::parse($data->end_time)->setTimezone($currentTimezone);   
                $availability[] = [
                    'day_of_week' => $data->day, // Monday
                    'start_time' => $startTimeInCounselorTimezone->format('H:i'),
                    'end_time' => $endTimeInCounselorTimezone->format('H:i'),
            ];
           }
        }
        return response()->json([
            'timeZones' => $currentTimezone, // Replace with saved timezone
            'availability' => $availability,
        ]);
    }
    public function setAvailability(Request $request)
    {
        $validated = $request->validate([
            'availability' => 'present|array', // Make sure availability data is in JSON format
            // 'timezone' => 'nullable|string', // Make sure timezone is a nullable string (optional)
        ]);

        $availabilityData = $validated['availability'];
        $user_id = $request->counselorId;
        $counselor = Counselor::findOrFail($user_id);

        // if (isset($validated['timezone'])) {
        //     $counselor->timezone = $validated['timezone'];
        //     $counselor->save();
        // }
        \DB::beginTransaction();

        try {
            $counselor->availabilities()->delete();

            foreach ($availabilityData as $dayAvailability) {
                if ($dayAvailability['start_time'] && $dayAvailability['end_time']) {
                    $startTime = Carbon::parse($dayAvailability['start_time'], $counselor->timezone);
                    $endTime = Carbon::parse($dayAvailability['end_time'], $counselor->timezone);

                    $startTimeUtc = $startTime->setTimezone('UTC');
                    $endTimeUtc = $endTime->setTimezone('UTC');

                    $startTimeForSave = $startTimeUtc->format('H:i:s');  // Store only the time in H:i:s format
                    $endTimeForSave = $endTimeUtc->format('H:i:s');  // Store only the time in H:i:s format


                    Availability::create([
                        'counselor_id' => $counselor->id,
                        'day' => $dayAvailability['day_of_week'],
                        'start_time' => $startTimeForSave,
                        'end_time' => $endTimeForSave,
                        'available' => true,
                    ]);
                }
            }

            // Update the counselor's timezone (if provided)
            // if ($validated['timezone']) {
            //     $counselor->timezone = $validated['timezone'];
            //     $counselor->save();
            // }

            // Generate slots based on new availability
            $counselor->slots()->where('is_booked', false)->delete();
            app(SlotGenerationService::class)->generateSlotsForCounselor($counselor);
            \DB::commit();
            return redirect()->route('counseller.availability')->with('success', 'Availability updated successfully.');
        } catch (\Exception $e) {
            \DB::rollBack();

            \Log::error('Error updating availability', ['error' => $e->getMessage()]);
            return redirect()->route('counseller.availability')->with('error', 'An error occurred while updating availability. Please try again later.');
        }
    }
    public function saveTimezone(Request $request)
    {
       
        $validated = $request->validate([
            'timezone' => 'nullable|string', // Make sure timezone is a nullable string (optional)
        ]);
        $user = Auth::guard('counselor')->user();
        $user_id = isset($request->counselorId) ? $request->counselorId : $user->id??null;
        $counselor = Counselor::findOrFail($user_id);
        DB::beginTransaction();
        try {
            if ($validated['timezone']) {
                $counselor->timezone = $validated['timezone'];
                $counselor->save();
            }
            DB::commit();
            app(SlotGenerationService::class)->generateSlotsForCounselor($counselor);
            return response()->json(['message' => 'Time zone saved successfully!'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 200);
        }
    }

    public function timezones(){
        $path = public_path('mw-1' . DIRECTORY_SEPARATOR . 'timezones.json');
        $json = File::get($path);
        $timezones = json_decode($json, true);
        return $timezones;
    }

    public function counsellerProfile()
    {
        $Counselor = Auth::guard('counselor')->user();
        $timezones = $this->timezones();
        $path = public_path('mw-1' . DIRECTORY_SEPARATOR . 'timezones.json');
        $json = File::get($path);
        $timezones = json_decode($json, true);

        return view('mw-1.counseller.counsellor-profile', get_defined_vars());
    }

    public function profileSave(Request $request)
    {
        $request->validate([
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'location' => 'required|string', 
            'language' => 'required|array', 
            'language.*' => 'string',
        ]);
        $Counselor = Auth::guard('counselor')->user();
        $specilization = json_decode($Counselor->specialization);
        if(isset($request->tags) && $request->tags != '')
        {
            $specilization = explode(",", $request->tags);
        }
        // $Counselor->timezone = $request->timezone;
        $Counselor->description = $request->description;
        $Counselor->gender = $request->gender;
        $Counselor->intake_link = $request->intake_link;
        $Counselor->notice_period = $request->notice_period;
        $Counselor->language = json_encode($request->language);
        $Counselor->location = $request->location;

        $imageName = '';
        if ($request->hasFile('logo')) {
            $image = $request->file('logo'); // Use `file()` for clarity
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('logo', $imageName); // Saves to storage/logo
            $Counselor->avatar = $imageName;
        }
        $Counselor->specialization = json_encode($specilization);
        $Counselor->communication_method = json_encode($request->communication_methods);
        $Counselor->save();
        if(isset($request->notice_period))
        {
            $Counselor->slots()->where('is_booked', false)->delete();
            $month = now()->addMonth()->month;
            app(SlotGenerationService::class)->generateSlotsForCounselor($Counselor);
            app(SlotGenerationService::class)->generateSlotsForCounselor($Counselor,$month);
            
        }
        return back()->with(['message' => "Information Saved Successfully"]);
    }
    public function setting()
    {

       
        $counselor = Auth::guard('counselor')->user();

        // Pass current 2FA state, secret, and QR code (if enabled)
        $qrCodeUrl = null;
        $secret = null;

        if ($counselor->uses_two_factor_auth) {
            $google2fa = new Google2FA();
            $secret = $counselor->google2fa_secret;
            $qrCodeUrl = $google2fa->getQRCodeUrl(
                config('app.name'),
                $counselor->email,
                $secret
            );
        }
        return view('mw-1.counseller.counsellor-setting', compact('qrCodeUrl', 'secret', 'counselor'));
    }
    public function counsellerCancelSession(Request $request)
    {

        $booking = Booking::findOrFail($request['booking_id']);
        if ($booking->status == 'cancelled') {
            return redirect()->back()->with('error', 'Booking already cancelled successfully');
        }
        $user_id = $request['customer_id'];
        if ((int)$booking->user_id !== (int)$user_id) {
            return redirect()->back()->with('error', 'Not allowed to cancel the session');
        }

        $booking->update(['status' => 'cancelled']);
        $booking->slot->update(['is_booked' => false]);
        return redirect()->back()->with('success', 'Booking cancelled successfully');
    }
    public function counsellerSettingSave(Request $request)
    {
       
        $counselor = Auth::guard('counselor')->user();
        $google2fa = new Google2FA();

        if ($request->has('enable_2fa')) {
            
            if(!$counselor->uses_two_factor_auth)
            {
                $secret = $google2fa->generateSecretKey();
                $counselor->google2fa_secret = $secret;
                $counselor->uses_two_factor_auth = true;
                $counselor->save();
            }
            
            $qrCodeUrl = $google2fa->getQRCodeUrl(
                config('app.name'),
                $counselor->email,
                $counselor->google2fa_secret
            );
            return redirect()->route('counseller.setting')
                ->with(['success' => 'Two-factor authentication enabled!', 'qrCodeUrl' => $qrCodeUrl, 'secret' => $counselor->google2fa_secret]);
        } else {
            // Disable 2FA
            $counselor->google2fa_secret = null;
            $counselor->uses_two_factor_auth = false;
            $counselor->save();

            return redirect()->route('counseller.setting')
                ->with(['success' => 'Two-factor authentication disabled!']);
        }
    }
}
