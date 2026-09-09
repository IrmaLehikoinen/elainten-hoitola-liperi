<?php

namespace App\Console\Commands;

use App\Modules\Ajanvaraus\Models\CalendarBlock;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class DebugCalendarRender extends Command
{
    protected $signature = 'ajanvaraus:debug-render {date=2026-09-24}';

    protected $description = 'Diagnoosi: toistaa kalenterin lohkologiikan yhdelle päivälle komentoriviltä, ohi selaimen ja näkymän.';

    public function handle(): int
    {
        $date = Carbon::createFromFormat('Y-m-d', $this->argument('date'))->startOfDay();
        $monthStart = $date->copy()->startOfMonth();
        $monthEnd = $date->copy()->endOfMonth();

        $this->info('Tarkistettava päivä: '.$date->toDateString());
        $this->info('Kuukausiväli: '.$monthStart->toDateString().' - '.$monthEnd->toDateString());

        $blocksInRange = CalendarBlock::whereBetween('date', [$monthStart->toDateString(), $monthEnd->toDateString()])->get();
        $this->info('Lohkoja koko kuukaudessa: '.$blocksInRange->count());

        $dayBlocks = $blocksInRange->filter(fn ($b) => $b->date->isSameDay($date))->values();
        $this->info('Lohkoja juuri tälle päivälle: '.$dayBlocks->count());

        foreach ($dayBlocks as $block) {
            $this->line('  -> company '.$block->company_id.', date-tyyppi: '.get_class($block->date).', reason: '.$block->reason);
        }

        return self::SUCCESS;
    }
}