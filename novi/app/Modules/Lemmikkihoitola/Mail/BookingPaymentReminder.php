<?php

namespace App\Modules\Lemmikkihoitola\Mail;

use App\Modules\Lemmikkihoitola\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingPaymentReminder extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Booking $booking)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Muistutus: varauksesi maksu tänään',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.booking-payment-reminder',
        );
    }
}