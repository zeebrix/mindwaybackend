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
use GPBMetadata\Google\Api\Auth;
use Illuminate\Support\Facades\DB;

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
    public function getCounselorsPagination(Request $request)
    {
        $customer = Customer::with(['Program' => function ($query) {
            $query->limit(1); // Fetch only one related object
        }])->with('preference')->find($request->customer_id);

        $preference = $customer ? $customer->preference : null;
        $recommendedCounselors = [];
        $page = $request->page ?? 1;
        if ($preference && $page == 1) {
            $bindings = [];
        
            // Prepare gender placeholders and bindings
            $genderPlaceholders = implode(',', array_fill(0, count($preference->gender), '?'));
            $bindings = array_merge($bindings, $preference->gender);
        
            // Prepare specialization bindings
            $bindings = array_merge($bindings, $preference->specializations);
        
            // Prepare communication method bindings
            $bindings = array_merge($bindings, $preference->communication_methods);
        
            // Prepare language bindings
            $bindings = array_merge($bindings, (array)$preference->language);
        
            // Add location to bindings
            $bindings[] = $preference->location;
            // dd($bindings);
            // Create the main query with proper scoring and prioritization
            $counselors = Counselor::select('*')
                ->selectRaw("
                (
                    CASE WHEN gender IN ($genderPlaceholders) THEN 30 ELSE 0 END +
        
                    (
                        SELECT COUNT(*) * 10 
                        FROM JSON_TABLE(specialization, '$[*]' COLUMNS (spec VARCHAR(255) PATH '$')) AS jt
                        WHERE jt.spec IN (" . implode(',', array_fill(0, count($preference->specializations), '?')) . ")
                    ) +
        
                    (
                        SELECT COUNT(*) * 20 
                        FROM JSON_TABLE(communication_method, '$[*]' COLUMNS (method VARCHAR(255) PATH '$')) AS jt
                        WHERE jt.method IN (" . implode(',', array_fill(0, count($preference->communication_methods), '?')) . ")
                    ) +
        
                    (
                        SELECT COUNT(*) * 40 
                        FROM JSON_TABLE(language, '$[*]' COLUMNS (lang VARCHAR(255) PATH '$')) AS jl
                        WHERE jl.lang IN (" . implode(',', array_fill(0, count((array)$preference->language), '?')) . ")
                    ) +
        
                    CASE WHEN location = ? THEN 50 ELSE 0 END
                ) as match_score
            ", $bindings)
                ->havingRaw('match_score > 0') // Only get counselors with at least one match
                ->orderByDesc('match_score')
                ->orderBy('id')
                ->limit(3) // Get only top 3 matches
                ->get();
                dd($counselors->toArray());
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
