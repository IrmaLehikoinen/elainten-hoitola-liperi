<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'company_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * MALLIESIMERKKI: käyttäjän lisäyritykset kotiyrityksen (company_id)
     * lisäksi, kohdassa 1 luodun company_user-taulun kautta.
     */
    public function additionalCompanies()
    {
        return $this->belongsToMany(Company::class);
    }

    /**
     * Kaikki yritykset joihin käyttäjällä on pääsy: kotiyritys +
     * lisäyritykset, ilman kaksoiskappaleita. Useimmilla käyttäjillä
     * tässä on vain yksi yritys (heidän kotiyrityksensä).
     */
    public function accessibleCompanies()
    {
        return collect([$this->company])
            ->merge($this->additionalCompanies)
            ->filter()
            ->unique('id')
            ->values();
    }
}