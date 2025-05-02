<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AttendanceAlertEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $messageContent;

    public function __construct($message)
    {
        $this->messageContent = $message;
    }

    public function build()
    {
        Log::info('messageContent:', ['messageContent' => $this->messageContent]);
        return $this->subject('Alerta de Asistencia')
                    ->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'))
                    ->view('emails.attendance_alert')
                    ->with([
                        'messages' => $this->messageContent,
                    ]);
    }
}
