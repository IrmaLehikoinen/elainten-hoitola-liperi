<?php

namespace App\Modules\Kurssit\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class CourseRegistration extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'course_id',
        'name',
        'email',
        'phone',
        'status',
        'payment_deadline',
        'payment_method',
        'payment_choice',
        'paid_at',
        'invoice_number',
        'issued_at',
        'reminder_sent_at',
    ];

    protected $casts = [
        'payment_deadline' => 'datetime',
        'paid_at' => 'datetime',
        'issued_at' => 'datetime',
        'reminder_sent_at' => 'datetime',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
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