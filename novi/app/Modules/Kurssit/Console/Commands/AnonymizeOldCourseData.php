<?php

namespace App\Modules\Kurssit\Console\Commands;

use App\Modules\Kurssit\Models\CourseRegistration;
use App\Modules\Kurssit\Models\GiftCard;
use Illuminate\Console\Command;

class AnonymizeOldCourseData extends Command
{
    protected $signature = 'kurssit:anonymize-old-data';
    protected $description = 'Poistaa vanhat peruutetut ilmoittautumiset ja anonymisoi vanhat asiakastiedot GDPR:n säilytysrajoitusperiaatteen mukaisesti';

    public function handle(): int
    {
        // Peruutetut ilmoittautumiset, joista ei ole syntynyt kirjanpitovelvoitetta,
        // voidaan poistaa kokonaan kun ne ovat riittävän vanhoja.
        $deleted = CourseRegistration::where('status', 'cancelled')
            ->where('updated_at', '<', now()->subMonths(12))
            ->delete();

        // Maksetut ilmoittautumiset pitää säilyttää kirjanpitolain edellyttämät
        // 6 vuotta laskutustietojen vuoksi, mutta henkilötiedot voidaan
        // anonymisoida sen jälkeen.
        $anonymizedRegistrations = CourseRegistration::where('status', 'confirmed')
            ->where('created_at', '<', now()->subYears(6))
            ->where('name', '!=', '[Poistettu]')
            ->update([
                'name' => '[Poistettu]',
                'email' => 'poistettu@poistettu.invalid',
                'phone' => null,
            ]);

        // Loppuun käytetyt tai vanhentuneet lahjakortit: anonymisoidaan
        // ostajan tiedot kun kortti on riittävän vanha.
        $anonymizedGiftCards = GiftCard::where(function ($query) {
                $query->where('balance', '<=', 0)
                    ->orWhere('valid_until', '<', now());
            })
            ->where('created_at', '<', now()->subYears(6))
            ->where('purchaser_name', '!=', '[Poistettu]')
            ->update([
                'purchaser_name' => '[Poistettu]',
                'purchaser_email' => 'poistettu@poistettu.invalid',
            ]);

        $this->info("{$deleted} vanhaa peruutettua ilmoittautumista poistettu.");
        $this->info("{$anonymizedRegistrations} vanhaa ilmoittautumista anonymisoitu.");
        $this->info("{$anonymizedGiftCards} vanhaa lahjakorttia anonymisoitu.");

        return self::SUCCESS;
    }
}