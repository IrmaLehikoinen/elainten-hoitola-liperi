<?php

namespace App\Modules\Ajanvaraus\Mail;

use App\Modules\Ajanvaraus\Models\TreatmentAppointment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class GroupSessionAtRisk extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public TreatmentAppointment $appointment,
        public int $currentCount,
        public int $minParticipants,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Huom: '.$this->appointment->treatment->name.' saattaa perua osallistujapulan vuoksi',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'ajanvaraus::emails.group-at-risk',
        );
    }
}