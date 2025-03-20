<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Availability;
use App\Models\Counselor;
use App\Models\CounselorAvailability;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Services\SlotGenerationService;
use App\Services\CounselorService;
use App\Services\CalendarService;
use Illuminate\Support\Facades\DB;

class CounselorController extends Controller
{
    private $counselorService;
    private $calendarService;
    public function __construct(CounselorService $counselorService,CalendarService $calendarService)
    {
        $this->counselorService = $counselorService;
        $this->calendarService = $calendarService;
    }

    public function getCounselors(Request $request)
    {
        $customer = Customer::with(['Program' => function ($query) {
            $query->limit(1); // Fetch only one related object
        }])->with('reserveSlot')->find($request->customer_id);
        $gender = $customer->gender_preference??'Male';
        $recommendedCounselor=  $this->counselorService->getRecommendedCounselors(
            $customer->id??0,
            $gender ?? 'Male'
        );
        $allCounsellor = $this->counselorService->getAllCounselors();
        return response()->json([ 
            'recommended_counselor' => $recommendedCounselor,
            'all_counselors' => $allCounsellor,
            'customer' => $customer,
        ]);
    }
    public function getCounselorsPagination(Request $request)
    {
        $customer = Customer::with(['Program' => function ($query) {
            $query->limit(1); // Fetch only one related object
        }])->with('preference')->find($request->customer_id);
       
        $preference = $customer ? $customer->preference : null;
        $recommendedCounselors = [];
        $page =$request->page??1;
        if ($preference && $page == 1) {
            $bindings = [
                $preference->language, // String
                $preference->location // String
            ];
            
            // Build JSON_SEARCH conditions for multiple values in specialization
            $specializationConditions = [];
            foreach ($preference->specializations as $specialization) {
                $specializationConditions[] = 'JSON_SEARCH(specialization, "one", ?)';
                $bindings[] = $specialization; // Add each specialization separately
            }
            
            // Build JSON_SEARCH conditions for multiple values in communication_method
            $communicationConditions = [];
            foreach ($preference->communication_methods as $method) {
                $communicationConditions[] = 'JSON_SEARCH(communication_method, "one", ?)';
                $bindings[] = $method; // Add each communication method separately
            }
            
            // Step 1: Create a Subquery with Match Score Calculation
            $subQuery = Counselor::select('*')
                ->selectRaw('
                    (CASE WHEN gender IN (' . implode(',', array_fill(0, count($preference->gender), '?')) . ') THEN 3 ELSE 0 END) +
                    (CASE WHEN (' . implode(' OR ', $specializationConditions) . ') THEN 1 ELSE 0 END) +
                    (CASE WHEN (' . implode(' OR ', $communicationConditions) . ') THEN 2 ELSE 0 END) +
                    (CASE WHEN language = ? THEN 4 ELSE 0 END) +
                    (CASE WHEN location = ? THEN 5 ELSE 0 END)
                    AS match_score
                ', array_merge($preference->gender, $bindings));
            
            $rawSql = $subQuery->toSql(); // Get raw SQL query
            
            // Step 2: Find the Maximum Match Score
            $maxScoreQuery = DB::table(DB::raw("({$rawSql}) as ranked_counselors"))
                ->mergeBindings($subQuery->getQuery()) // Merge bindings properly
                ->selectRaw('MAX(match_score) as max_score')
                ->value('max_score');
            
            $maxScore = $maxScoreQuery ?? 0; // Default to 0 if no matches
            
            // Step 3: Retrieve Counselors with the Highest Match Score
            $recommendedCounselors = Counselor::select('*')
                ->selectRaw('
                    (CASE WHEN gender IN (' . implode(',', array_fill(0, count($preference->gender), '?')) . ') THEN 3 ELSE 0 END) +
                    (CASE WHEN (' . implode(' OR ', $specializationConditions) . ') THEN 1 ELSE 0 END) +
                    (CASE WHEN (' . implode(' OR ', $communicationConditions) . ') THEN 2 ELSE 0 END) +
                    (CASE WHEN language = ? THEN 4 ELSE 0 END) +
                    (CASE WHEN location = ? THEN 5 ELSE 0 END)
                    AS match_score
                ', array_merge($preference->gender, $bindings))
                ->having('match_score', '=', $maxScore) // Keep only the best matches
                ->orderByDesc('match_score')
                ->orderBy('id')
                ->get();
            

        
            $recommendedCounselors = $this->counselorService->formatCounselors($recommendedCounselors);
        }
        $allCounselors = $this->counselorService->getAllCounselors(
            pagination: $request->pagination??true, // Default: true
            page: $page,
            offset: $request->offset??15,
            location:$request->location??null
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
        $user_id = session('user_id')??4;
        $counselor = Counselor::where('id',$user_id )->firstOrFail();
        
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
            ->reject(fn($item) => $item === "")
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