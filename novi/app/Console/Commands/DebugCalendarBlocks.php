<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Modules\Ajanvaraus\Models\CalendarBlock;
use Illuminate\Console\Command;

class DebugCalendarBlocks extends Command
{
    protected $signature = 'ajanvaraus:debug-blocks';

    protected $description = 'Diagnoosi: tulostaa calendar_blocks-taulun sisällön ja yrityksen active_modules-tiedon.';

    public function handle(): int
    {
        $company = Company::where('industry', 'lemmikkihoitola')->first();

        $this->info('Yritys: '.($company->name ?? '???').' (id '.($company->id ?? '?').'), active_modules: '.json_encode($company->active_modules ?? []));

        $blocks = CalendarBlock::orderBy('date')->get();

        $this->info('CalendarBlock-rivejä yhteensä: '.$blocks->count());

        foreach ($blocks as $block) {
            $this->line($block->company_id.' | '.$block->date->format('Y-m-d').' | '.($block->start_time ?? 'koko päivä').'-'.($block->end_time ?? '').' | '.$block->reason);
        }

        return self::SUCCESS;
    }
}