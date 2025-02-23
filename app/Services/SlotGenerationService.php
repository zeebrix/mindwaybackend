<?php

namespace App\Services;

use App\Models\Counselor;
use App\Models\Slot;
use Carbon\Carbon;

class SlotGenerationService
{
    private const SLOT_DURATION = 50; // minutes
    private GoogleProvider $googleProvider;
    public function __construct(GoogleProvider $googleProvider)
    {
        $this->googleProvider = $googleProvider;
    }
    public function generateSlotsForCounselor(Counselor $counselor, $month = null)
    {
        if ($month) {
            $startDate = now()->setTimezone('UTC')->setDay(1)->setMonth($month)->startOfMonth();
            $endDate = $startDate->copy()->endOfMonth();
            $existingSlots = $counselor->slots()
                ->whereBetween('date', [
                    $startDate->toDateString(),
                    $endDate->toDateString()
                ])
                ->where('is_booked', false)
                ->whereNull('customer_id')
                ->exists();
            if ($existingSlots) {
                return;
            }
        } else {
            $month = now()->month;
            $startDate = now()->setTimezone('UTC')->setDay(1)->setMonth($month)->startOfMonth();
            $endDate = $startDate->copy()->endOfMonth();
        }

        // Delete future slots that aren't booked
        $counselor->slots()
            ->whereBetween('date', [
                $startDate->toDateString(),
                $endDate->toDateString()
            ])
            ->where('is_booked', false)
            ->whereNull('customer_id')
            ->delete();
        $timezone = $counselor->timezone;
        $startDate = $month ? now()->setTimezone($timezone)->setDay(1)->setMonth($month)->startOfMonth() : now()->setTimezone($timezone)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();
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
        $this->removeConflictingSlots($counselor, $month);
        return;
    }

    private function generateSlotsForDay(
        Counselor $counselor,
        Carbon $date,
        $startTime,
        $endTime,
        string $timezone
    ) {
        $startTime = $startTime->setTimezone($timezone);
        $endTime = $endTime->setTimezone($timezone);
        $start = Carbon::parse($startTime, $timezone)->setDateFrom($date);
        $end = Carbon::parse($endTime, $timezone)->setDateFrom($date);
        if ($start->minute > 0 || $start->second > 0) {
            $start = $start->addHour()->minute(0)->second(0);
        }
        while ($start->copy()->addMinutes(self::SLOT_DURATION) <= $end) {
            $slotStart = $start->copy()->setTimezone('UTC');
            $slotEnd = $start->copy()->addMinutes(self::SLOT_DURATION)->setTimezone('UTC');
            $slot = Slot::where('counselor_id', $counselor->id)->where('start_time', $slotStart)->where('end_time', $slotEnd)->first();
            if (!$slot) {
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
    public function removeConflictingSlots(Counselor $counselor, ?string $month = null)
    {
        try {
            // Ensure the counselor has a Google token
            if (!$counselor->googleToken || !$counselor->googleToken->access_token) {
                \Log::warning("Google Token missing for counselor ID: {$counselor->id}");
                return;
            }
    
            $timezone = $counselor->timezone ?? 'UTC';
    
            if ($month) {
                $startOfMonth = Carbon::now($timezone)->setDay(1)->setMonth($month)->startOfMonth();
                $endOfMonth = $startOfMonth->copy()->endOfMonth();
            } else {
                // If no month is provided, check the entire year (Jan 1 - Dec 31)
                $startOfMonth = Carbon::now($timezone)->startOfYear();
                $endOfMonth = Carbon::now($timezone)->endOfYear();
            }
    
            // Fetch all events for the given range
            $events = $this->googleProvider->getAllEvents(
                $counselor->googleToken->access_token,
                $startOfMonth->toRfc3339String(),
                $endOfMonth->toRfc3339String()
            );
    
            if (empty($events)) {
                \Log::info("No events found for counselor ID: {$counselor->id} in range: {$startOfMonth->format('Y-m-d')} - {$endOfMonth->format('Y-m-d')}");
                return;
            }
    
            // Convert event times to UTC
            foreach ($events as &$event) {
                try {
                    $event['start_time'] = Carbon::parse($event['start_time'], $event['start_timezone'] ?? $timezone)->setTimezone('UTC');
                    $event['end_time'] = Carbon::parse($event['end_time'], $event['end_timezone'] ?? $timezone)->setTimezone('UTC');
                } catch (\Exception $e) {
                    \Log::error("Error parsing event times for event ID: {$event['event_id']}");
                    continue;
                }
            }
    
            // Fetch all slots for the counselor in the given range
            $slots = Slot::where('counselor_id', $counselor->id)
                ->whereBetween('start_time', [$startOfMonth->setTimezone('UTC'), $endOfMonth->setTimezone('UTC')])
                ->get();
    
            // Remove slots that overlap with events
            foreach ($slots as $slot) {
                foreach ($events as $event) {
                    if ($slot->start_time < $event['end_time'] && $slot->end_time > $event['start_time']) {
                        \Log::info("Deleting slot ID: {$slot->id} as it conflicts with event ID: {$event['event_id']}");
                        $slot->delete();
                        break; // No need to check further, slot is already deleted
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::error("Error in removeConflictingSlots for counselor ID: {$counselor->id}, range: {$startOfMonth->format('Y-m-d')} - {$endOfMonth->format('Y-m-d')}. Exception: " . $e->getMessage());
        }
    }
    
    public function removeConflictingSlots1(Counselor $counselor, string $month=null)
    {
        try {
            // Ensure the counselor has a Google token
            if (!$counselor->googleToken || !$counselor->googleToken->access_token) {
                \Log::warning("Google Token missing for counselor ID: {$counselor->id}");
                return;
            }

            $timezone = $counselor->timezone ?? 'UTC';
            $startOfMonth = Carbon::now($timezone)->setDay(1)->setMonth($month)->startOfMonth();
            $endOfMonth = $startOfMonth->copy()->endOfMonth();

            // Fetch all events for the given month
            $events = $this->googleProvider->getAllEvents(
                $counselor->googleToken->access_token,
                $startOfMonth->toRfc3339String(),
                $endOfMonth->toRfc3339String()
            );

            if (empty($events)) {
                \Log::info("No events found for counselor ID: {$counselor->id} in month: {$month}");
                return;
            }

            // Convert event times to UTC
            foreach ($events as &$event) {
                try {
                    $event['start_time'] = Carbon::parse($event['start_time'], $event['start_timezone'] ?? $timezone)->setTimezone('UTC');
                    $event['end_time'] = Carbon::parse($event['end_time'], $event['end_timezone'] ?? $timezone)->setTimezone('UTC');
                } catch (\Exception $e) {
                    \Log::error("Error parsing event times for event ID: {$event['event_id']}");
                    continue;
                }
            }

            // Fetch all slots for the counselor in the given month
            $slots = Slot::where('counselor_id', $counselor->id)->where('is_booked', false)
                ->whereBetween('start_time', [$startOfMonth->setTimezone('UTC'), $endOfMonth->setTimezone('UTC')])
                ->get();

            // Remove slots that overlap with events
            foreach ($slots as $slot) {
                foreach ($events as $event) {
                    if ($slot->start_time < $event['end_time'] && $slot->end_time > $event['start_time']) {
                        \Log::info("Deleting slot ID: {$slot->id} as it conflicts with event ID: {$event['event_id']}");
                        $slot->delete();
                        break; // No need to check further, slot is already deleted
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::error("Error in removeConflictingSlots for counselor ID: {$counselor->id}, month: {$month}. Exception: " . $e->getMessage());
        }
    }
}
