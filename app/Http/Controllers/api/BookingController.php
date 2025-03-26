<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Counselor;
use App\Models\Customer;
use App\Models\Slot;
use App\Models\CustomreBrevoData;
use Illuminate\Http\Request;
use App\Services\GoogleProvider;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
class BookingController extends Controller
{
    protected $googleProvider;

    public function __construct(GoogleProvider $googleProvider)
    {
        $this->googleProvider = $googleProvider;
    }
    public function getAvailableSlots(Request $request)
    {
        $validated = $request->validate([
            'counselor_id' => 'required|exists:counselors,id',
            'date' => 'nullable|date|after_or_equal:today',
        ]);
        $counselor = Counselor::where('id',$validated['counselor_id'])->first();
        $notice_period = isset($counselor) ?$counselor->notice_period : 12;
        $customer_timezone = $request->customer_timezone; // Use proper timezone string
        $date = $validated['date'] ?? now()->toDateString(); // Date in YYYY-MM-DD format
        
        // Convert customer timezone date to UTC start and end of the day
        $startDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $date . ' 00:00:00', $customer_timezone)
            ->setTimezone('UTC')
            ->format('Y-m-d H:i:s');
        
        $endDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $date . ' 23:59:59', $customer_timezone)
            ->setTimezone('UTC')
            ->format('Y-m-d H:i:s');

        $slots = Slot::where('counselor_id', $validated['counselor_id'])
            ->whereBetween('start_time', [$startDateTime, $endDateTime])
            ->where('is_booked', false)
            ->whereNull('customer_id')
            ->where('start_time', '>', now()->addHours($notice_period))
            ->orderBy('start_time')
            ->get();

        return response()->json($slots);
    }

    public function bookSlot(Request $request)
    {
        $validated = $request->validate([
            'counselor_id' => 'required|exists:counselors,id',
            'slot_id' => 'required|exists:slots,id',
            'customer_id' => 'required',
            'communication_method' => 'required',
        ]);
        DB::beginTransaction(); // Start a new transaction
        try{
        $counselor = Counselor::where("id",$validated['counselor_id'])->first();
        $notice_period = isset($counselor) ?$counselor->notice_period : 12;
        $customer_timezone = isset($request->customer_timezone) ? $request->customer_timezone : 'UTC';
        $customer = Customer::where('id', $request->customer_id)->first();
        try {
           $customer->timezone = $customer_timezone;
           $customer->save();
        } catch (\Throwable $th) {
            Log::info('Timezone saving error'.$th->getMessage());
        }
        if ($customer->max_session <= 0) {
            return response()->json([
                'message' => 'You have reached to the max session limit'
            ], 400);
        }
        $slot = Slot::where('id', $validated['slot_id'])
        ->where('counselor_id', $validated['counselor_id'])
        ->where('is_booked', false)
        ->where('customer_id',$validated['customer_id'])
        ->first();
        if(!$slot)
        {
            return response()->json([
                'message' => 'This Slot is not available.'
            ], 422);
            
        }
        if ($slot->start_time <= now()->addHours($notice_period)) {
            return response()->json([
                'message' => 'Slot must be booked at least 24 hours in advance'
            ], 422);
        }
        $booking_start_time = $slot->start_time;
        $booking_end_time = $slot->end_time;
        $conflictingBooking = Booking::where('user_id', $customer->id)
            ->whereHas('slot', function ($query) use ($booking_start_time) {
            $query->where('start_time', $booking_start_time);
        })
        ->first();
        if($conflictingBooking)
        {
            return response()->json([
                'message' => 'You have already a booking at this time..'
            ], 422);
            
        }
        $customer->phone = $request->phone ?? '';
        //$customer->max_session = $customer->max_session - 1;
        $customer->save();
        
        

        $booking = Booking::create([
            'user_id' => $customer->id,
            'counselor_id' => $validated['counselor_id'],
            'slot_id' => $validated['slot_id'],
            'status' => 'confirmed',
            'communication_method' => $validated['communication_method']
        ]);

        $slot->update(['is_booked' => true]);
        $counselor = Counselor::where("id",$validated['counselor_id'])->first();
        $meetingLink = '';
        $eventId = '';
        try {
            if($counselor?->googleToken?->access_token)
            {
                $eventData = [
                    'title' => '50min Mindway EAP Session',
                    'description' => "<p><strong>Important Information:</strong></p>
                <p>You can cancel or update your session up to 24 hours before the session. While rare, bookings may be subject to changes; you'll be contacted to select another date. Any information shared remains confidential between you and your counsellor.</p>
                
                <p><strong>Stored Information:</strong></p>
                <p>Your details and session history are kept for historical and management purposes.</p>
                
                <p><strong>In Case of Emergency:</strong></p>
                <p>This service is not designed for emergencies. If you are in crisis or facing an immediate threat to yourself or others, please contact your local emergency services or crisis hotline immediately.</p>
                
                <p><strong>Emergency Hotline:</strong> Lifeline Australia: 13 11 14</p>",
                    'start_time' => Carbon::parse($booking_start_time)->setTimezone($counselor->timezone),
                    'end_time' =>  Carbon::parse($booking_end_time)->setTimezone($counselor->timezone),
                     'counselor_email' => $counselor->email,
                    'employee_email' => $customer->email,
                    'timezone' => $counselor->timezone,
                    'access_token' => $counselor->googleToken->access_token,
                    'communication_method' => $validated['communication_method']
                ];
                // send to customer 
                $event = $this->googleProvider->createEvent($eventData);
                $meetingLink = $event['meeting_link'];
                $eventId = $event['event_id'];
            
            }
        } catch (\Throwable $th) {
            Log::error('An error occurred', [
        'message' => $th->getMessage(),
        'file' => $th->getFile(),
        'line' => $th->getLine(),
        'trace' => $th->getTraceAsString(),
    ]);
        }
        $booking->event_id = $eventId;
        $booking->meeting_link = $meetingLink;
        $booking->save();
        // to employee
        if(!$booking->brevoUser)
        {
            $brevo = new CustomreBrevoData();
            $brevo->name = $booking->user->name;
            $brevo->email = $booking->user->email;
            $brevo->program_id = $booking?->user?->single_program?->id;
            $brevo->company_name = $booking?->user?->single_program?->company_name;
            $brevo->max_session = $booking?->user?->single_program?->max_session;
            $brevo->is_app_user = true;
            $brevo->app_customer_id = $booking->user->id;
            $brevo->save();
            
        }
        DB::commit();
        $recipient = $customer->email;
        $subject = 'Session Confirmed With '.$counselor->name;
        $template = 'emails.booking-confirmation-employee';
        $data = [
            'communication_method' => $booking->communication_method,
            'full_name' => $customer->name,
            'counselor_name' => $counselor->name,
            'start_time' => Carbon::parse($slot->start_time)->setTimezone($customer_timezone),
            'timezone' => $customer_timezone,
            'duration' => '50 minutes',
            'meeting_link' => $meetingLink,
            'max_session' => $customer->max_session,
            'intake_link' => $counselor->intake_link,
            'phone' => $request->phone??'',
        ];
        sendDynamicEmailFromTemplate($recipient, $subject, $template, $data);

        // counselor 
        $recipient = $counselor->email;
        $subject = 'New Session Scheduled';
        $template = 'emails.booking-confirmation-counselor';
        $data = [
            'communication_method' => $booking->communication_method,
             'employee_email' => $booking?->user?->email,
            'employee_phone' => $booking?->user?->phone,
            'full_name' => $counselor->name,
            'client_name' => $customer->name,
            'company_name' => $booking?->user?->single_program?->company_name,
            'start_time' => Carbon::parse($slot->start_time)->setTimezone($counselor->timezone),
            'max_session' => $customer->max_session,
            'meeting_link' => $meetingLink,
            'timezone' => $counselor->timezone,
            'phone' => $request->phone??'',
        ];
        sendDynamicEmailFromTemplate($recipient, $subject, $template, $data);
         
          return response()->json($booking->load('slot'));
}
catch (\Exception $e) {
    // If anything goes wrong, roll back the transaction
    DB::rollBack();
    Log::error('An error occurred in book slot', [
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString(),
    ]);
    // Optionally, log the error or return a response
   return response()->json(['message'=> $e->getMessage()]);
}
      
    }

    public function rescheduleSlot(Request $request)
    {
        $validated = $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'new_slot_id' => 'required|exists:slots,id',
            'customer_id' => 'required',
            'communication_method' => 'required',
        ]);

        $booking = Booking::findOrFail($validated['booking_id']);
        $user_id = $validated['customer_id'];
        if ((int)$booking->user_id !== (int)$user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $target = Counselor::where('id',$booking->counselor_id)->first();
        $notice_period = isset($target) ?$target->notice_period : 12;
        
        $newSlot = Slot::findOrFail($validated['new_slot_id']);
        if ($newSlot->is_booked || $newSlot->start_time <= now()->addHours($notice_period)) {
            return response()->json([
                'message' => 'Selected slot is not available'
            ], 422);
        }
        $booking->slot->update(['is_booked' => false]);
        $booking->slot_id = $newSlot->id;
        $booking->status = 'confirmed';
        $booking->communication_method = $validated['communication_method'];
        $booking->save();

        $newSlot->update(['is_booked' => true]);
        $booking = Booking::findOrFail($validated['booking_id']);
        $eventId = $booking->event_id;
        $meetingLink = $booking->meeting_link;
        try {
            $eventData = [
                'title' => '50min Mindway EAP Session',
                'description' => "<p><strong>Important Information:</strong></p>
            <p>You can cancel or update your session up to 24 hours before the session. While rare, bookings may be subject to changes; you'll be contacted to select another date. Any information shared remains confidential between you and your counsellor.</p>
            
            <p><strong>Stored Information:</strong></p>
            <p>Your details and session history are kept for historical and management purposes.</p>
            
            <p><strong>In Case of Emergency:</strong></p>
            <p>This service is not designed for emergencies. If you are in crisis or facing an immediate threat to yourself or others, please contact your local emergency services or crisis hotline immediately.</p>
            
            <p><strong>Emergency Hotline:</strong> Lifeline Australia: 13 11 14</p>",
                'start_time' =>  Carbon::parse($booking->slot->start_time)->setTimezone($booking->counselor->timezone),
                'end_time' => Carbon::parse($booking->slot->end_time)->setTimezone($booking->counselor->timezone),
                'counselor_email' => $booking->counselor->email,
                'employee_email' => $booking->user->email,
                'timezone' => $booking->counselor->timezone,
                'access_token' => $booking->counselor->googleToken->access_token,
                'update_meeting_link' => true,
                'communication_method' => $validated['communication_method']
            ];
            if($booking->event_id)
            {
                $event = $this->googleProvider->updateEvent($booking->event_id , $eventData);
            }
            else
            {
                $event = $this->googleProvider->createEvent($eventData);
            }
            $meetingLink = $event['meeting_link'];
            $eventId = $event['event_id'];
        } catch (\Throwable $th) {
        Log::error('An error occurred', [
            'message' => $th->getMessage(),
            'file' => $th->getFile(),
            'line' => $th->getLine(),
            'trace' => $th->getTraceAsString(),
            ]);
        }
        
        
        $customer_timezone = isset($request->customer_timezone) ? $request->customer_timezone : 'UTC';
        $booking->event_id = $eventId;
        $booking->meeting_link = $meetingLink;
        $booking->save();
        $recipient = $booking->user->email;
        try {
            Customer::where('email',$recipient)->update(['timezone'=>$customer_timezone]);
        } catch (\Throwable $th) {
            Log::info('Timezone saving error'.$th->getMessage());
        }
        $subject = 'Your Session Has Been Rescheduled';
        $template = 'emails.counsellor-slot-rescheduled-employee';
        $data = [
            'communication_method' => $validated['communication_method'],
            'full_name' => $booking->user->name,
            'counselor_name' => $booking->counselor->name,
            'start_time' => Carbon::parse($booking->slot->start_time)->setTimezone($customer_timezone),
            'end_time' => Carbon::parse($booking->slot->end_time)->setTimezone($customer_timezone),
            'timezone' => $customer_timezone,
            'meeting_link' => $meetingLink,
            'intake_link' => $booking->counselor->intake_link??'',
            'phone' => $booking->user->phone??''
        ];
        sendDynamicEmailFromTemplate($recipient, $subject, $template, $data);

        // couselor 
        $recipient = $booking->counselor->email;
        $subject = 'Your Session Has Been Rescheduled';
        $template = 'emails.counsellor-slot-rescheduled-counselor';
        $data = [
            'communication_method' => $validated['communication_method'],
            'employee_email' => $booking->user->email,
            'employee_phone' => $booking->user->phone,
            'full_name' => $booking->counselor->name,
            'customer_name' => $booking->user->name,
            'max_session' => $booking?->brevoUser?->max_session??'',
            'timezone' => $booking->counselor->timezone,
            'meeting_link' => $meetingLink,
            'phone' => $booking->user->phone??'',
            'start_time' => Carbon::parse($booking->slot->start_time)->setTimezone($booking->counselor->timezone),
            'company_name' => $booking?->brevoUser?->program?->company_name,
        ];
        sendDynamicEmailFromTemplate($recipient, $subject, $template, $data);

       
        return response()->json($booking->load('slot'));
    }
    public function reservedSlot(Request $request)
    {
        $validated = $request->validate([
            'slot_id' => 'required|exists:slots,id',
            'customer_id' => 'required',
        ]);

        $slot = Slot::where('is_booked',false)
                    ->where('id',$validated['slot_id'])
                    ->whereNull('customer_id')
                    ->first();
        if(!$slot)
        {
            return response()->json(['message' => 'Slot already booked or reserved try different one.'], 400);
        }        
        Slot::where('is_booked',false)->where('id','!=',$slot->id)->where('customer_id',$validated['customer_id'])->update(['customer_id'=>null]);
        $slot->customer_id = $validated['customer_id'];
        $slot->save();
        return response()->json(['message' => 'Slot Reserved Successfully'], 200);
    }
    public function cancelBooking(Request $request)
    {
        $validated = $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'customer_id' => 'required',
        ]);
        $booking = Booking::findOrFail($validated['booking_id']);
        if ($booking->status == 'cancelled') {
            return response()->json(['message' => 'Booking Already cancelled successfully']);
        }
        $user_id = $validated['customer_id'];
        if ((int)$booking->user_id !== (int)$user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $booking->update(['status' => 'cancelled']);
        $booking->slot->update(['is_booked' => false]);
        try {
            $this->googleProvider->deleteEvent($booking->event_id, $booking->counselor->googleToken->access_token);
        } catch (\Throwable $th) {
            //throw $th;
        }
        $customer_timezone = isset($request->customer_timezone) ? $request->customer_timezone : 'UTC';
        $recipient = $booking->user->email;
        $subject = 'Cancelled Session Notification';
        $template = 'emails.cancel-session-employee';
        $data = [
            'full_name' => $booking->user->name,
            'counselor_name' => $booking->counselor->name,
            'start_time' => Carbon::parse($booking->slot->start_time)->setTimezone($customer_timezone),
            'timezone' => $customer_timezone,
        ];
        sendDynamicEmailFromTemplate($recipient, $subject, $template, $data);

        // couselor 
        $recipient = $booking->counselor->email;
        $subject = 'Cancelled Session Notification';
        $template = 'emails.cancel-session-counselor';
        $data = [
            'full_name' => $booking->counselor->name,
            'customer_name' => $booking->user->name,
            'max_session' => $booking->user->max_session,
            'timezone' => $booking->counselor->timezone,
            'start_time' => Carbon::parse($booking->slot->start_time)->setTimezone($booking->counselor->timezone),
            'company_name' => $booking->brevoUser->program->company_name,
        ];
        sendDynamicEmailFromTemplate($recipient, $subject, $template, $data);




        return response()->json(['message' => 'Booking cancelled successfully']);
    }
}
