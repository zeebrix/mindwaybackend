<?php

namespace App\Services;

use App\Models\Counselor;
use App\Models\Slot;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class CalendarService
{
    public function getMonthlyAvailability(int $counselorId, int $month, int $year): array
    {
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();
        // Get all slots for the month
        $slots = Slot::where('counselor_id', $counselorId)
            ->whereBetween('date', [$startDate, $endDate])
            ->where('start_time', '>', now()->addHours(24))
            ->get()
            ->groupBy(function ($slot) {
                return $slot->date->format('Y-m-d'); // Ensure date is formatted as a string
            });

        $calendar = [];
        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            // Skip dates in the past
            if ($currentDate < now()->startOfDay()) {
                $currentDate->addDay();
                continue;
            }

            
            $dateStr = $currentDate->format('Y-m-d');
            $daySlots = $slots->get($dateStr, collect());
            if($hasSlots = $daySlots->whereNull('customer_id')->where('is_booked', false)->whereNull('customer_id')->isNotEmpty())
            {
                $calendar[] = [
                    'date' => $dateStr,
                    'day_of_week' => $currentDate->dayOfWeek,
                    'has_slots' => $hasSlots,
                    'first_slot_time' => $daySlots->first()->start_time,
                    'available_slots_count' => $daySlots->where('is_booked', false)->whereNull('customer_id')->count(),
                    'total_slots_count' => $daySlots->count(),
                ];
            }
            $currentDate->addDay();
        }

        return [
            'month' => $month,
            'year' => $year,
            'dates' => $calendar
        ];
    }
}