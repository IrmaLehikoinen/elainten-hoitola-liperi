<?php

namespace App\Modules\Kurssit\Mail;

use App\Modules\Kurssit\Models\CourseRegistration;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CourseRegistrationExpired extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public CourseRegistration $registration)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Ilmoittautumisesi on peruuntunut',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'kurssit::emails.registration-expired',
        );
    }
}