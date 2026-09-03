<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateNewCustomer extends Command
{
    protected $signature = 'novi:new-customer';

    protected $description = 'Luo uuden yrityksen ja sen ensimmäisen pääkäyttäjän uutta asiakasasennusta varten';

    public function handle(): int
    {
        $this->info('Uuden asiakkaan käyttöönotto');
        $this->line('---------------------------');

        $existing = Company::count();
        if ($existing > 0) {
            $this->warn("Tässä asennuksessa on jo {$existing} yritys(tä). Tämä komento on tarkoitettu uuden, tyhjän asennuksen ensimmäiselle yritykselle.");
            if (! $this->confirm('Jatketaanko silti?')) {
                return self::FAILURE;
            }
        }

        $name = $this->ask('Yrityksen nimi (esim. "Vaahteran Hierontapiste")');

        $industries = array_unique(array_merge(array_keys(config('industries', [])), ['kurssit']));

        $industry = $this->choice('Toimiala', array_values($industries), 0);

        $email = $this->ask('Yrityksen sähköposti (näkyy asiakkaille, esim. laskuissa)');

        $company = Company::create([
            'name' => $name,
            'industry' => $industry,
            'email' => $email,
            'slug' => Str::slug($name),
        ]);

        $this->info("Yritys luotu: {$company->name} (id: {$company->id})");

        $this->line('');
        $this->line('Nyt luodaan pääkäyttäjätunnus, jolla yrittäjä kirjautuu ensimmäistä kertaa sisään.');

        $adminName = $this->ask('Pääkäyttäjän nimi');
        $adminEmail = $this->ask('Pääkäyttäjän sähköposti (kirjautumistunnus)');
        $adminPassword = $this->secret('Pääkäyttäjän salasana (vähintään 8 merkkiä, vaihdetaan käyttöön otettaessa)');

        $user = User::create([
            'name' => $adminName,
            'email' => $adminEmail,
            'password' => Hash::make($adminPassword),
            'company_id' => $company->id,
            'email_verified_at' => now(),
        ]);

        $this->info("Pääkäyttäjä luotu: {$user->email}");
        $this->line('');
        $this->info('Valmista! Yrittäjä voi kirjautua osoitteessa /login näillä tunnuksilla.');
        $this->warn('Muista vielä käsin: lisää oikeat STRIPE_KEY / STRIPE_SECRET / STRIPE_WEBHOOK_SECRET .env-tiedostoon, ja aseta yrityksen brändiväritykset ja logo Asetukset-sivulta kirjautumisen jälkeen.');

        return self::SUCCESS;
    }
}