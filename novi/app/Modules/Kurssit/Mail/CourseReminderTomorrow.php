<?php

namespace App\Modules\Kurssit\Mail;

use App\Modules\Kurssit\Models\CourseRegistration;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CourseReminderTomorrow extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public CourseRegistration $registration)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Muistutus: kurssisi on huomenna',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'kurssit::emails.reminder-tomorrow',
        );
    }
}