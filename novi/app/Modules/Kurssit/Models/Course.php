<?php

namespace App\Modules\Kurssit\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'name',
        'short_description',
        'presentation_type',
        'description_html',
        'brochure_path',
        'content_blocks',
        'starts_at',
        'price',
        'max_participants',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'price' => 'decimal:2',
        'content_blocks' => 'array',
    ];

    public function registrations()
    {
        return $this->hasMany(CourseRegistration::class);
    }

    /**
     * Vapaat paikat: maksimi miinus (vahvistetut + vielä voimassa olevat
     * maksua odottavat) ilmoittautumiset. Ei koskaan negatiivinen.
     */
    public function remainingSpots(): int
    {
        $activeCount = $this->registrations()
            ->where(function ($query) {
                $query->where('status', 'confirmed')
                    ->orWhere(function ($query) {
                        $query->where('status', 'pending')
                            ->where('payment_deadline', '>', now());
                    });
            })
            ->count();

        return max(0, $this->max_participants - $activeCount);
    }

        public function isFull(): bool
    {
        return $this->remainingSpots() <= 0;
    }

    public function confirmedCount(): int
    {
        return $this->registrations()->where('status', 'confirmed')->count();
    }

    /**
     * Onko kurssi täynnä vain kesken jääneiden, vielä maksamattomien
     * ilmoittautumisten vuoksi (ei yhtään todellista vahvistettua täyttä
     * paikkamäärää)? Näytetään tällöin kannustava "kokeile myöhemmin"
     * -viesti pelkän "täynnä"-viestin sijaan.
     */
    public function isTemporarilyFull(): bool
    {
        return $this->isFull() && $this->confirmedCount() < $this->max_participants;
    }
}    