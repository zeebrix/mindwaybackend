<?php

namespace App\Services;

use App\Models\Counselor;
use App\Models\Slot;
use Carbon\Carbon;

class SlotGenerationService
{
    private const SLOT_DURATION = 50; // minutes

    public function generateSlotsForCounselor(Counselor $counselor,$month = null)
    {
        $timezone = 'UTC';
        $startDate = $month ? now()->setTimezone($timezone)->setMonth($month)->startOfMonth() : now()->setTimezone($timezone)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();
        if($month)
        {
            $existingSlots = $counselor->slots()
            ->whereBetween('date', [
                $startDate->copy()->setTimezone('UTC')->toDateString(), 
                $endDate->copy()->setTimezone('UTC')->toDateString()
            ])
            ->where('is_booked',false)
            ->whereNull('customer_id')
            ->exists();
            if ($existingSlots) {
                return;
            }
        }
        // Delete future slots that aren't booked
        $counselor->slots()
            ->whereBetween('date', [
                $startDate->copy()->setTimezone('UTC')->toDateString(), 
                $endDate->copy()->setTimezone('UTC')->toDateString()
            ])
            ->where('is_booked', false)
            ->whereNull('customer_id')
            ->delete();
        while ($startDate <= $endDate) {
            $dayOfWeek = ($startDate->dayOfWeek + 6) % 7;
           

            // Get availability for this day
            $availability = $counselor->availabilities()
                ->where('day', $dayOfWeek)
                ->get();
               
            foreach ($availability as $schedule) {
                $this->generateSlotsForDay(
                    $counselor,
                    $startDate,
                    $schedule->start_time,
                    $schedule->end_time,
                    $timezone
                );
            }
            
            $startDate->addDay();
        }
        return ;
    }

    private function generateSlotsForDay(
        Counselor $counselor,
        Carbon $date,
        $startTime,
        $endTime,
        string $timezone
    ) {
        $start = Carbon::parse($startTime, $timezone)->setDateFrom($date);
        $end = Carbon::parse($endTime, $timezone)->setDateFrom($date);
        if ($start->minute > 0 || $start->second > 0) {
            $start = $start->addHour()->minute(0)->second(0);
        }
        while ($start->copy()->addMinutes(self::SLOT_DURATION) <= $end) {
            $slotStart = $start->copy()->setTimezone('UTC');
            $slotEnd = $start->copy()->addMinutes(self::SLOT_DURATION)->setTimezone('UTC');
            $slot = Slot::where('counselor_id',$counselor->id)->where('start_time',$slotStart)->where('end_time',$slotEnd)->first();
            if(!$slot)
            {
                 Slot::create([
                    'counselor_id' => $counselor->id,
                    'date' => $slotStart->toDateString(),
                    'start_time' => $slotStart,
                    'end_time' => $slotEnd,
                    'is_booked' => false,
                ]);
            }
           
            $start->addHour();
        }
    }
}