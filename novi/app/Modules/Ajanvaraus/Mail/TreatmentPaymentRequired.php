<?php

namespace App\Modules\Ajanvaraus\Mail;

use App\Modules\Ajanvaraus\Models\TreatmentAppointment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TreatmentPaymentRequired extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public TreatmentAppointment $appointment, public string $paymentUrl)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Varauksesi odottaa maksua',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'ajanvaraus::emails.payment-required',
        );
    }
}