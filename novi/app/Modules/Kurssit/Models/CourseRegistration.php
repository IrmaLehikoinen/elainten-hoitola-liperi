<?php

namespace App\Modules\Kurssit\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class CourseRegistration extends Model
{
    use BelongsToCompany;

    protected static function booted(): void
    {
        static::creating(function ($registration) {
            if (empty($registration->payment_token)) {
                $registration->payment_token = \Illuminate\Support\Str::random(40);
            }
        });
    }

    protected $fillable = [
        'company_id',
        'course_id',
        'name',
        'email',
        'phone',
        'status',
        'cancellation_reason',
        'payment_deadline',
        'payment_method',
        'payment_choice',
        'paid_at',
        'refunded_at',
        'checked_in_at',
        'invoice_number',
        'issued_at',
        'reminder_sent_at',
        'gift_card_id',
        'gift_card_amount',
        'gift_card_applied_at',
    ];

    protected $casts = [
        'payment_deadline' => 'datetime',
        'paid_at' => 'datetime',
        'refunded_at' => 'datetime',
        'checked_in_at' => 'datetime',
        'issued_at' => 'datetime',
        'reminder_sent_at' => 'datetime',
        'gift_card_amount' => 'decimal:2',
        'gift_card_applied_at' => 'datetime',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function giftCard()
    {
        return $this->belongsTo(GiftCard::class);
    }

    /**
     * Kurssin hinta miinus käytetyn lahjakortin summa. Ei koskaan negatiivinen.
     */
    public function amountDue(): float
    {
        return max(0, (float) $this->course->price - (float) ($this->gift_card_amount ?? 0));
    }

    /**
     * Vähentää lahjakortin saldon vasta kun maksu on lopullisesti
     * vahvistettu — ei ilmoittautumishetkellä. Kutsutaan aina kun
     * ilmoittautuminen merkitään maksetuksi/vahvistetuksi. Suojattu
     * tuplavähennykseltä gift_card_applied_at-aikaleimalla.
     */
    public function applyGiftCardIfNeeded(): void
    {
        if (! $this->gift_card_id || $this->gift_card_applied_at) {
            return;
        }

        $giftCard = $this->giftCard;

        if ($giftCard) {
            $giftCard->update([
                'balance' => max(0, (float) $giftCard->balance - (float) $this->gift_card_amount),
            ]);
        }

        $this->update(['gift_card_applied_at' => now()]);
    }

    public function referenceNumber(): string
    {
        $base = str_pad((string) $this->id, 6, '0', STR_PAD_LEFT);
        $weights = [7, 3, 1];
        $sum = 0;

        foreach (str_split(strrev($base)) as $index => $digit) {
            $sum += ((int) $digit) * $weights[$index % 3];
        }

        $checkDigit = (10 - ($sum % 10)) % 10;

        return $base.$checkDigit;
    }

    public function dueDate()
    {
        $days = (int) ($this->company->settings['payment_term_days'] ?? 14);

        return $this->issued_at?->copy()->addDays($days);
    }
}