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
        $allCounsellor = $this->counselorService->getAllCounselors(
            $gender ?? 'Male'
        );
        return response()->json([ 
            'recommended_counselor' => $recommendedCounselor,
            'all_counselors' => $allCounsellor,
            'customer' => $customer,
        ]);
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
}