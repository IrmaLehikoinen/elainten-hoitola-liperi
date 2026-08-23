<?php

namespace App\Mail;

use App\Modules\Lemmikkihoitola\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingPaymentExpired extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Booking $booking)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Varauksesi on peruuntunut',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.booking-payment-expired',
        );
    }
}