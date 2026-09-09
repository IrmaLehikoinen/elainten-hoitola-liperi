<?php

namespace App\Console\Commands;

use App\Models\Company;
use Illuminate\Console\Command;

class FixActiveModules extends Command
{
    protected $signature = 'novi:fix-active-modules';

    protected $description = 'Kertaluonteinen korjaus: lisää "ajanvaraus" lemmikkihoitola-yritysten active_modules-kenttään, jotta moduulien välinen kalenterisynkka toimii.';

    public function handle(): int
    {
        $companies = Company::where('industry', 'lemmikkihoitola')->get();

        if ($companies->isEmpty()) {
            $this->warn('Ei löytynyt lemmikkihoitola-yrityksiä.');
            return self::SUCCESS;
        }

        foreach ($companies as $company) {
            $modules = $company->active_modules ?? [$company->industry];

            if (! in_array('ajanvaraus', $modules, true)) {
                $modules[] = 'ajanvaraus';
            }

            $company->update(['active_modules' => array_values(array_unique($modules))]);

            $this->info($company->name.': active_modules = ['.implode(', ', $company->active_modules).']');
        }

        return self::SUCCESS;
    }
}