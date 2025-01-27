<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Booking;

class BookingCancellation extends Notification
{
    private $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        
        return (new MailMessage)
        ->subject('Cancelled Session Notification')
        ->line('Hi ' . $this->booking->user->name . ',')
        ->line('The session with ' . $this->booking->counselor->name . ' scheduled for ' . $this->booking->slot->start_time->format('H:i') . ' on ' . $this->booking->slot->date->format('d/m/Y') . ' ('.$this->booking->counselor->timezone.') has been cancelled.')
        ->line('Client Details:')
        ->line('Client Name: ' . $this->booking->user->name)
        ->line('Company: ' . $this->booking->user->company)
        ->line('Sessions Remaining: ' . $this->booking->user->max_session)
        ->line('Thank you,')
        ->line('Mindway EAP Bookings Team');
    
    }
}