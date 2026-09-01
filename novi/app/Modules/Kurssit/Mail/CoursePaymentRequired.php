<?php

namespace App\Modules\Kurssit\Mail;

use App\Modules\Kurssit\Models\CourseRegistration;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CoursePaymentRequired extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public CourseRegistration $registration, public string $paymentUrl)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Kurssi-ilmoittautumisesi odottaa maksua',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'kurssit::emails.payment-required',
        );
    }
}