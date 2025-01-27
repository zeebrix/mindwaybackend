<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Booking;

class BookingConfirmation extends Notification
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
            ->subject('Booking Confirmation')
            ->line('Your counseling session has been confirmed.')
            ->line('Date: ' . $this->booking->slot->date)
            ->line('Time: ' . $this->booking->slot->start_time->format('H:i'))
            ->line('Thank you for using our service!');
    }
}