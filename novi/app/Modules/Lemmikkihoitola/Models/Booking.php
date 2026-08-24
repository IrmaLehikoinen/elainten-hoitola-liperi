<?php

namespace App\Modules\Lemmikkihoitola\Models;

use App\Models\Concerns\BelongsToCompany;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * MALLIESIMERKKI: tämä on lemmikkihoitolan oma toteutus geneerisestä
 * "varaus"-käsitteestä (asiakas + ajanjakso + hinta + maksu). Toinen
 * toimiala (esim. parturi) EI voi käyttää tätä suoraan — sillä on oma
 * varauskäsitteensä (esim. "Appointment") — mutta tämän tiedoston
 * RAKENNE (kentät, tilat pending/confirmed/cancelled, ennakkomaksuvirta)
 * kannattaa katsoa mallina.
 */
class Booking extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'customer_id',
        'arrival_at',
        'pickup_at',
        'care_type',
        'responsible_user_id',
        'resource_id',
        'start_date',
        'end_date',
        'status',
        'payment_status',
        'priority',
        'confirmation_channel',
        'acknowledged_at',
        'invoice_skipped_at',
        'total_price',
        'price_estimate',
       'deposit_amount',
        'deposit_paid_at',
        'payment_deadline',
        'locked_until',
        'send_email_confirmation',
        'send_sms_confirmation',
        'notes',
    ];

    protected $casts = [
        'arrival_at' => 'datetime',
        'pickup_at' => 'datetime',
        'start_date' => 'date',
        'end_date' => 'date',
        'total_price' => 'decimal:2',
        'price_estimate' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
        'deposit_paid_at' => 'datetime',
        'payment_deadline' => 'datetime',
        'locked_until' => 'datetime',
        'acknowledged_at' => 'datetime',
        'invoice_skipped_at' => 'datetime',
        'send_email_confirmation' => 'boolean',
        'send_sms_confirmation' => 'boolean',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function participants()
    {
        return $this->hasMany(BookingParticipant::class);
    }

    public function bookingServices()
    {
        return $this->hasMany(BookingService::class);
    }

    public function resource()
    {
        return $this->belongsTo(Resource::class);
    }

    public function responsibleUser()
    {
        return $this->belongsTo(User::class, 'responsible_user_id');
    }

    public function historyEntries()
    {
        return $this->hasMany(PetHistoryEntry::class);
    }

    public function reminders()
    {
        return $this->hasManyThrough(
            Reminder::class,
            BookingParticipant::class,
            'booking_id',
            'booking_participant_id'
        );
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }
}