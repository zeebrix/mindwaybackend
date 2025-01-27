<?php
use App\Mail\SendEmail;
use Illuminate\Support\Facades\Mail;

function sendDynamicEmailFromTemplate($recipient, $subject, $template, $data)
{
    $data['subject'] = $subject; // Add subject to data
    try {
        Mail::to($recipient)->send(new SendEmail($template, $data));
        return ['success' => true, 'message' => 'Email sent successfully.'];
    } catch (\Exception $e) {
        \Log::error('Email sending failed: ' . $e->getMessage());
        return ['success' => false, 'message' => 'Failed to send email.'];
    }
}

