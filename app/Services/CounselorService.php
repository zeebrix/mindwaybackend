<?php

namespace App\Services;

use App\Models\Counselor;
use App\Models\Booking;
use App\Models\Slot;
use Illuminate\Support\Facades\DB;

class CounselorService
{
    public function getAllCounselors(bool $pagination = false, int $page = 1, int $offset = 10 , ?string $location = null)
    {
        $query = $this->baseCounselorQuery();
        if ($pagination) {
            if ($location) {
                $query->orderByRaw("CASE WHEN location = ? THEN 1 ELSE 2 END", [$location]);
            }
            $data = $query
                    ->orderBy('location')
                    ->orderBy('next_available_slot')
                    ->paginate($offset, ['*'], 'page', $page);
            return $this->formatCounselorsPagination($data);
        } else {
            $data = $query->orderByRaw('next_available_slot asc')->get();
            return $this->formatCounselors($data);
        }
        
    }

    public function getRecommendedCounselors(int $userId, ?string $gender = null)
    {
        $query = $this->baseCounselorQuery()
            ->leftJoin('bookings', function ($join) use ($userId) {
                $join->on('counselors.id', '=', 'bookings.counselor_id')
                    ->where('bookings.user_id', '=', $userId);
            })
            ->addSelect([
                DB::raw('COUNT(bookings.id) as session_count')
            ])
            ->groupBy('counselors.id');
        if ($gender) {
            $query->where('gender', $gender);
        }
        $data =  $this->formatCounselors(
            $query
                ->orderBy('session_count', 'desc')
                ->orderByRaw('next_available_slot asc')
                ->get()
        );
        if (count($data)) {
            return $data[0];
        }
        return null;
    }

    private function baseCounselorQuery()
    {
        return Counselor::whereHas('availabilities')
            ->addSelect([
                'next_available_slot' => Slot::select('start_time')
                    ->whereColumn('counselor_id', 'counselors.id')
                    ->where('start_time', '>', DB::raw('NOW() + INTERVAL counselors.notice_period HOUR')) // Add the counselor's dynamic notice period
                    ->where('is_booked', false)
                    ->whereNull('customer_id')
                    ->orderBy('start_time', 'asc')
                    ->limit(1)
            ]);
    }
    public function formatCounselors($counselors)
    {
        return collect($counselors)->map(function ($counselor) {
            
            return [
                'id' => $counselor->id,
                'name' => $counselor->name,
                'email' => $counselor->email,
                'gender' => $counselor->gender??'Male',
                'bio' => $counselor->description,
                'intake_link' => $counselor->intake_link,
                'avatar' => $counselor->avatar,
                'location' => $counselor->location,
                'language' => json_decode($counselor->language),
                'intro_file' => $counselor->intro_file,
                'specialization' => $counselor->specialization??json_encode([]),
                'communication_method' => $counselor->communication_method,
                'about_session' => [
                    'session_time' => '50min Session',
                    'session_topic' => 'Free professional support by employer',
                    'encryption' => 'Bookings are confidential and not shared with your employer',
                    'detail' => "<p><strong>Important Information:</strong></p>
        <p>You can cancel or update your session up to 24 hours before the session. While rare, bookings may be subject to changes; you'll be contacted to select another date. Any information shared remains confidential between you and your counsellor.</p>
        
        <p><strong>Stored Information:</strong></p>
        <p>Your details and session history are kept for historical and management purposes.</p>
        
        <p><strong>In Case of Emergency:</strong></p>
        <p>This service is not designed for emergencies. If you are in crisis or facing an immediate threat to yourself or others, please contact your local emergency services or crisis hotline immediately.</p>
        
        <p><strong>Emergency Hotline:</strong> Lifeline Australia: 13 11 14</p>"
                ],
                'hourly_rate' => $counselor->hourly_rate,
                'timezone' => $counselor->timezone??'Australia/Adelaide',
                'next_availability' => $counselor->next_available_slot ? [
                    'available_day' => \Carbon\Carbon::parse($counselor->next_available_slot)->format('L'),
                    'date' => \Carbon\Carbon::parse($counselor->next_available_slot),
                    'start_time' => \Carbon\Carbon::parse($counselor->next_available_slot),
                ] : null,
                'session_count' => $counselor->session_count ?? 0
            ];
        });
    }

    private function getFormatedCounsellorFromCounsellorObject($counselor){
                    
        return [
            'id' => $counselor->id,
            'name' => $counselor->name,
            'email' => $counselor->email,
            'gender' => $counselor->gender??'Male',
            'bio' => $counselor->description,
            'intake_link' => $counselor->intake_link,
            'avatar' => $counselor->avatar,
            'location' => $counselor->location,
            'language' => json_decode($counselor->language),
            'intro_file' => $counselor->intro_file,
            'specialization' => json_decode($counselor->specialization) == NULL ? json_encode([]) : $counselor->specialization??json_encode([]),
            'communication_method' => $counselor->communication_method,
            'about_session' => [
                'session_time' => '50min Session',
                'session_topic' => 'Free professional support by employer',
                'encryption' => 'Bookings are confidential and not shared with your employer',
                'detail' => "<p><strong>Important Information:</strong></p>
    <p>You can cancel or update your session up to 24 hours before the session. While rare, bookings may be subject to changes; you'll be contacted to select another date. Any information shared remains confidential between you and your counsellor.</p>
    
    <p><strong>Stored Information:</strong></p>
    <p>Your details and session history are kept for historical and management purposes.</p>
    
    <p><strong>In Case of Emergency:</strong></p>
    <p>This service is not designed for emergencies. If you are in crisis or facing an immediate threat to yourself or others, please contact your local emergency services or crisis hotline immediately.</p>
    
    <p><strong>Emergency Hotline:</strong> Lifeline Australia: 13 11 14</p>"
            ],
            'hourly_rate' => $counselor->hourly_rate,
            'timezone' => $counselor->timezone??'Australia/Adelaide',
            'next_availability' => $counselor->next_available_slot ? [
                'available_day' => \Carbon\Carbon::parse($counselor->next_available_slot)->format('L'),
                'date' => \Carbon\Carbon::parse($counselor->next_available_slot),
                'start_time' => \Carbon\Carbon::parse($counselor->next_available_slot),
            ] : null,
            'session_count' => $counselor->session_count ?? 0
        ];
    }

    private function formatCounselorsPagination($counselors)
    {
        // Check if paginated or collection
        $data = collect(
            $counselors instanceof \Illuminate\Pagination\LengthAwarePaginator ?
                $counselors->items() : $counselors
        )->map(function ($counselor) {
            return [
                'id' => $counselor->id,
                'name' => $counselor->name,
                'email' => $counselor->email,
                'gender' => $counselor->gender ?? 'Male',
                'bio' => $counselor->description,
                'intake_link' => $counselor->intake_link,
                'location' => $counselor->location,
                'language' => json_decode($counselor->language,true),
                'intro_file' => $counselor->intro_file,
                'avatar' => $counselor->avatar,
                'specialization' => json_decode($counselor->specialization, true) ?? [],
                'communication_method' => json_decode($counselor->communication_method, true) ?? [],
                'about_session' => [
                    'session_time' => '50min Session',
                    'session_topic' => 'Free professional support by employer',
                    'encryption' => 'Bookings are confidential and not shared with your employer',
                    'detail' => "<p><strong>Important Information:</strong></p>
        <p>You can cancel or update your session up to 24 hours before the session. While rare, bookings may be subject to changes; you'll be contacted to select another date. Any information shared remains confidential between you and your counsellor.</p>
        <p><strong>Stored Information:</strong></p>
        <p>Your details and session history are kept for historical and management purposes.</p>
        <p><strong>In Case of Emergency:</strong></p>
        <p>This service is not designed for emergencies. If you are in crisis or facing an immediate threat to yourself or others, please contact your local emergency services or crisis hotline immediately.</p>
        <p><strong>Emergency Hotline:</strong> Lifeline Australia: 13 11 14</p>"
                ],
                'hourly_rate' => $counselor->hourly_rate,
                'timezone' => $counselor->timezone ?? 'Australia/Adelaide',
                'next_availability' => $counselor->next_available_slot ? [
                    'available_day' => \Carbon\Carbon::parse($counselor->next_available_slot)->format('l'),
                    'date' => \Carbon\Carbon::parse($counselor->next_available_slot),
                    'start_time' => \Carbon\Carbon::parse($counselor->next_available_slot),
                ] : null,
                'session_count' => $counselor->session_count ?? 0
            ];
        });

        // If pagination, return paginated format
        return $counselors instanceof \Illuminate\Pagination\LengthAwarePaginator ?
            [
                'data' => $data,
                'pagination' => [
                    'total' => $counselors->total(),
                    'per_page' => $counselors->perPage(),
                    'current_page' => $counselors->currentPage(),
                    'last_page' => $counselors->lastPage(),
                ]
            ] : $data;
    }

    public function getUpcomingSessions(int $counselorId, int $limit = 10)
    {
        $upcomingBookings = Booking::with(['user', 'counselor', 'slot'])
            ->where('counselor_id', $counselorId)
            ->where('status', 'confirmed')
            ->whereHas('slot', function ($query) {
                $query->where('start_time', '>', now());
            })
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        return [
            'upcoming_sessions' => $upcomingBookings->map(function ($booking) {
                return [
                    'communication_method' => $booking->communication_method ?? '',
                    'meeting_link' => $booking->meeting_link,
                    'booking_id' => $booking->id,
                    'counselor' => $booking->counselor,
                    'user' => [
                        'id' => $booking->user?->id,
                        'name' => $booking?->user?->name,
                        'phone' => $booking?->user?->phone,
                    ],
                    'session_time' => [
                        'slot_id' => $booking->slot->id,
                        'date' => $booking->slot->date,
                        'start_time' => $booking->slot->start_time->format('H:i'),
                        'end_time' => $booking->slot->end_time->format('H:i'),
                    ],
                    'status' => $booking->status,
                    'booked_at' => $booking->created_at,
                ];
            }),
            'total_upcoming_sessions' => $upcomingBookings->count(),
        ];
    }
    public function getCustomerUpcomingSessions(int $customer_id, int $limit = 10)
    {
        $upcomingBookings = Booking::with(['user', 'counselor', 'slot'])
            ->where('user_id', $customer_id)
            ->where('status', 'confirmed')
            ->whereHas('slot', function ($query) {
                $query->where('start_time', '>', now());
            })
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        return [
            'upcoming_sessions' => $upcomingBookings->map(function ($booking) {
                return [
                    'communication_method' => $booking->communication_method ?? '',
                    'meeting_link' => $booking->meeting_link,
                    'booking_id' => $booking->id,
                    'counselor' => $this->getFormatedCounsellorFromCounsellorObject($booking->counselor),
                    'user' => [
                        'id' => $booking->user?->id,
                        'name' => $booking->user?->name,
                        'phone' => $booking?->user?->phone,
                    ],
                    'session_time' => [
                        'slot_id' => $booking->slot->id,
                        'date' => $booking->slot->date,
                        'start_time' => $booking->slot->start_time->format('H:i'),
                        'end_time' => $booking->slot->end_time->format('H:i'),
                    ],
                    'status' => $booking->status,
                    'booked_at' => $booking->created_at,
                ];
            }),
            'total_upcoming_sessions' => $upcomingBookings->count(),
        ];
    }
}
