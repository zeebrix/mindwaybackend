<?php

namespace App\Console\Commands;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendBookingReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder:send-booking';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send booking reminder emails to employees 12 hours before the session';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $bookings = Booking::whereHas('slot', function ($query) {
            $query->where('start_time', '<=', Carbon::now()->addHours(24))
                  ->where('start_time', '>', Carbon::now()->addHours(23));
        })->get();
        foreach ($bookings as $booking) {
            $customer = $booking->user;
            $counselor = $booking->counselor;
            $slot = $booking->slot;
            $meetingLink = $booking->meeting_link;
            $recipient = $customer->email;
            $subject = 'Reminder: Upcoming Session';
            $template = 'emails.booking-reminder-employee';
            $data = [
                'full_name' => $customer->name,
                'counselor_name' => $counselor->name,
                'start_time' => Carbon::parse($slot->start_time)->setTimezone($counselor->timezone),
                'timezone' => $customer->timezone,
                'meeting_link' => $meetingLink,
                'intake_link' => $counselor->intake_link
            ];
            sendDynamicEmailFromTemplate($recipient, $subject, $template, $data);
        }
    }
}
