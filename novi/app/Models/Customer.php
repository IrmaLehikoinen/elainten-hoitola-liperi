<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use BelongsToCompany;

  protected $fillable = ['company_id', 'name', 'email', 'phone', 'address', 'notes', 'custom_daily_rate']; 

    public function pets()
    {
        return $this->hasMany(Pet::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

        public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Poistaa asiakkaan henkilötiedot (tietosuoja-pyyntöä varten).
     * Jos kuitteja/laskuja on jo tehty, niitä ei saa hävittää
     * (kirjanpitolain säilytysvelvoite), joten silloin vain
     * henkilö- ja lemmikkitiedot anonymisoidaan, varaus-/laskuhistoria
     * säilyy ilman henkilötietoja.
     */
    public function eraseForPrivacy(): void
    {
        if ($this->invoices()->exists()) {
            \App\Models\BookingParticipant::whereHas('booking', function ($query) {
                $query->where('customer_id', $this->id);
            })->update(['name' => 'Poistettu', 'notes' => null]);

            $this->pets()->delete();

            $this->bookings()->update(['notes' => null]);

            $this->update([
                'name' => 'Poistettu asiakas',
                'email' => null,
                'phone' => null,
                'address' => null,
                'notes' => null,
            ]);

            return;
        }

        $this->delete();
    }
}