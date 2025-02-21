<?php
use App\Mail\SendEmail;
use Illuminate\Support\Facades\Mail;

function sendDynamicEmailFromTemplate($recipient, $subject, $template, $data)
{
    $data['subject'] = $subject; // Add subject to data
    try {
        Mail::to($recipient)->send(new SendEmail($template, $data));
        \Log::info('Email sent successfully', [
            'recipient' => $recipient ?? 'N/A',
            'subject' => $subject ?? 'N/A',
            'template' => isset($template) ? json_encode($template) : 'N/A',
            'email_data' => isset($data) ? json_encode($data) : 'N/A',
        ]);   
        return ['success' => true, 'message' => 'Email sent successfully.'];
    } catch (\Exception $e) {
        \Log::error('Email sending failed: ' . $e->getMessage());
        return ['success' => false, 'message' => 'Failed to send email.'];
    }
}

