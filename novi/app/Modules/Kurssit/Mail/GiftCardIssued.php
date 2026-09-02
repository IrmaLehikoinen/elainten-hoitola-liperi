<?php

namespace App\Modules\Kurssit\Mail;

use App\Modules\Kurssit\Models\GiftCard;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class GiftCardIssued extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public GiftCard $giftCard,
        public ?string $recipientName,
        public ?string $giftMessage,
        public ?string $purchaserName,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Sait lahjakortin!',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'kurssit::emails.gift-card-issued',
        );
    }
}