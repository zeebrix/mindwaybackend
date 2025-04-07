<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Availability;
use App\Models\Booking;
use App\Models\Counselor;
use App\Models\CounselorAvailability;
use App\Models\Customer;
use App\Models\Slot;
use Illuminate\Http\Request;
use App\Services\SlotGenerationService;
use App\Services\CounselorService;
use App\Services\CalendarService;
use GPBMetadata\Google\Api\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CounselorController extends Controller
{
    private $counselorService;
    private $calendarService;
    public function __construct(CounselorService $counselorService, CalendarService $calendarService)
    {
        $this->counselorService = $counselorService;
        $this->calendarService = $calendarService;
    }

    public function getCounselors(Request $request)
    {
        $customer = Customer::with(['Program' => function ($query) {
            $query->limit(1); // Fetch only one related object
        }])->with('reserveSlot')->find($request->customer_id);
        $gender = $customer->gender_preference ?? 'Male';
        $recommendedCounselor =  $this->counselorService->getRecommendedCounselors(
            $customer->id ?? 0,
            $gender ?? 'Male'
        );
        $allCounsellor = $this->counselorService->getAllCounselors();
        return response()->json([
            'recommended_counselor' => $recommendedCounselor,
            'all_counselors' => $allCounsellor,
            'customer' => $customer,
        ]);
    }
    public function getSanitizedPlaceholders($array) {
        $sanitizedArray = array_filter($array, function($value) {
            return $value !== null && $value !== '' && $value !== 'null';
        });
    
        // If empty, return a dummy placeholder to prevent SQL errors
        if (empty($sanitizedArray)) {
            return ['?', ['dummy_value']]; // Prevents "IN ()" error
        }
    
        $placeholders = implode(',', array_fill(0, count($sanitizedArray), '?'));
        return [$placeholders, $sanitizedArray];
    }
    
    public function getCounselorsPagination(Request $request)
    {
        Log::info('Customer_Id',$request->customer_id);
        $customer = Customer::with(['Program' => function ($query) {
            $query->limit(1); // Fetch only one related object
        }])->with('preference')->find($request->customer_id);

        $preference = $customer ? $customer->preference : null;
        $recommendedCounselors = [];
        $page = $request->page ?? 1;
       
        if ($page == 1) {
            $counselorIds = Booking::where('user_id',$request->customer_id)->where('status','!=','cancelled')->pluck('counselor_id');
            $counselors  = [];
            if(count($counselorIds))
            {
                $counselors = Counselor::whereHas('availabilities') ->addSelect([
                    'next_available_slot' => Slot::select('start_time')
                        ->whereColumn('counselor_id', 'counselors.id')
                        ->where('start_time', '>', DB::raw('NOW() + INTERVAL counselors.notice_period HOUR')) // Add the counselor's dynamic notice period
                        ->where('is_booked', false)
                        ->whereNull('customer_id')
                        ->orderBy('start_time', 'asc')
                        ->limit(1)
                ])
                ->whereIn('id',$counselorIds)
                ->orderBy('id')
                ->limit(3)->get();
            }
            if(empty($counselor) && $preference)
            {
                $bindings = [];

                list($genderPlaceholders, $genderBindings) = $this->getSanitizedPlaceholders($preference->gender);
                $bindings = array_merge($bindings, $genderBindings);
                
                list($specializationPlaceholders, $specializationBindings) = $this->getSanitizedPlaceholders($preference->specializations);
                $bindings = array_merge($bindings, $specializationBindings);
                
                list($communicationMethodPlaceholders, $communicationMethodBindings) = $this->getSanitizedPlaceholders($preference->communication_methods);
                $bindings = array_merge($bindings, $communicationMethodBindings);

                // Handle language bindings
                list($languagePlaceholders, $languageBindings) = $this->getSanitizedPlaceholders((array) $preference->language);
                $bindings = array_merge($bindings, $languageBindings);
                
                $location = $preference->location ?? ''; // Default to empty string if location is null
                $bindings[] = $location;
                $counselors = Counselor::whereHas('availabilities')
                ->select('*')
                ->addSelect([
                    'next_available_slot' => Slot::select('start_time')
                        ->whereColumn('counselor_id', 'counselors.id')
                        ->where('start_time', '>', DB::raw('NOW() + INTERVAL counselors.notice_period HOUR')) // Add the counselor's dynamic notice period
                        ->where('is_booked', false)
                        ->whereNull('customer_id')
                        ->orderBy('start_time', 'asc')
                        ->limit(1)
                ])
                ->selectRaw("
                (
                    CASE WHEN gender IN ($genderPlaceholders) THEN 30 ELSE 0 END +
                    COALESCE(
                        (SELECT COUNT(*) * 10 
                         FROM JSON_TABLE(specialization, '$[*]' COLUMNS (spec VARCHAR(255) PATH '$')) AS jt
                         WHERE jt.spec IN ($specializationPlaceholders)
                        ), 0) +
                    COALESCE(
                        (SELECT COUNT(*) * 20 
                         FROM JSON_TABLE(communication_method, '$[*]' COLUMNS (method VARCHAR(255) PATH '$')) AS jt
                         WHERE jt.method IN ($communicationMethodPlaceholders)
                        ), 0) +
                    COALESCE(
                        (SELECT COUNT(*) * 40 
                         FROM JSON_TABLE(language, '$[*]' COLUMNS (lang VARCHAR(255) PATH '$')) AS jl
                         WHERE jl.lang IN ($languagePlaceholders)
                        ), 0) +
                    CASE WHEN location = ? THEN 50 ELSE 0 END
                ) as match_score
            ", $bindings)
                    ->havingRaw('match_score > 0') // Only get counselors with at least one match
                    ->orderByDesc('match_score')
                    ->orderBy('id')
                    ->limit(3) // Get only top 3 matches
                    ->get();
                
            }
            $recommendedCounselors = $this->counselorService->formatCounselors($counselors);
        }

        $allCounselors = $this->counselorService->getAllCounselors(
            pagination: $request->pagination ?? true, // Default: true
            page: $page,
            offset: $request->offset ?? 15,
            location: $request->location ?? null
        );
        $response = [
            'all_counselors' => $allCounselors,
            'customer' => $customer,
        ];
        if ($page == 1) {
            $response['recommended_counselor'] = $recommendedCounselors ?? [];
        }

        return response()->json($response);
    }
    public function setAvailability(Request $request)
    {
        $validated = $request->validate([
            'availability' => 'required|array',
            'availability.*.day_of_week' => 'required|integer|between:0,6',
            'availability.*.start_time' => 'required|date_format:H:i',
            'availability.*.end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        $counselor = Auth::guard('counselor')->user();

        // Clear existing availability
        $counselor->availabilities()->delete();

        // Create new availability records
        foreach ($validated['availability'] as $schedule) {
            Availability::create([
                'counselor_id' => $counselor->id,
                'day' => $schedule['day_of_week'],
                'start_time' => $schedule['start_time'],
                'end_time' => $schedule['end_time'],
                'available' => true,
            ]);
        }
        // Generate slots based on new availability
        app(SlotGenerationService::class)->generateSlotsForCounselor($counselor);

        return response()->json(['message' => 'Availability updated successfully']);
    }
    public function getCalendarAvailability(Request $request)
    {
        $validated = $request->validate([
            'counselor_id' => 'required|exists:counselors,id',
            'month' => 'nullable|integer|between:1,12',
            'year' => 'nullable|integer|min:2024',
        ]);
        $month = $validated['month'] ?? now()->month;
        $counselor = Counselor::find($validated['counselor_id']);
        app(SlotGenerationService::class)->generateSlotsForCounselor($counselor, $month);
        $year = $validated['year'] ?? now()->year;

        return $this->calendarService->getMonthlyAvailability(
            $validated['counselor_id'],
            $month,
            $year
        );
    }
    public function getUpcomingSessions(Request $request)
    {
        $validated = $request->validate([
            'counselor_id' => 'required|exists:counselors,id',
            'limit' => 'nullable|integer|min:1|max:50',
        ]);

        $limit = $validated['limit'] ?? 10;

        return $this->counselorService->getUpcomingSessions(
            $validated['counselor_id'],
            $limit
        );
    }
    public function getCustomerUpcomingSessions(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'limit' => 'nullable|integer|min:1|max:50',
        ]);

        $limit = $validated['limit'] ?? 10;

        return $this->counselorService->getCustomerUpcomingSessions(
            $validated['customer_id'],
            $limit
        );
    }

    public function getPreferenceInfo()
    {
        $filters = Counselor::distinct()->get(['specialization', 'location', 'language', 'communication_method']);
        return response()->json([
            'specializations' => $filters->pluck('specialization')
                ->map(fn($item) => json_decode($item, true) ?? [])
                ->flatten()
                ->reject(fn($item) => $item === "")
                ->unique()
                ->values(),

            'locations' => $filters->pluck('location')
                ->filter()
                ->reject(fn($item) => $item === "")
                ->unique()
                ->values(),

            'languages' => $filters->pluck('language')
                ->filter()
                ->flatMap(fn($item) => is_string($item) ? (json_decode($item, true) ?? []) : (array) $item)
                ->unique()
                ->values(),

            'communication_methods' => $filters->pluck('communication_method')
                ->map(fn($item) => json_decode($item, true) ?? [])
                ->flatten()
                ->reject(fn($item) => $item === "")
                ->unique()
                ->values(),
        ]);
    }
}
