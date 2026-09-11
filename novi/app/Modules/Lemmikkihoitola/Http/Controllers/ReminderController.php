<?php

namespace App\Modules\Lemmikkihoitola\Http\Controllers;

use App\Events\ExternalTimeBlocked;
use App\Events\ExternalTimeUnblocked;
use App\Http\Controllers\Controller;
use App\Modules\Lemmikkihoitola\Models\Reminder;
use App\Modules\Lemmikkihoitola\Models\ReminderType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;

class ReminderController extends Controller
{
    /**
     * Lisää uuden muistutuksen eläinkortin "Hoitojakson muistutukset"
     * -osiosta. Sama rivi näkyy automaattisesti myös etusivun
     * "Muistutukset" -listalla, koska molemmat lukevat samaa
     * reminders-taulua.
     */
    public function store(Request $request)
    {
        $allowedTypes = ReminderType::pluck('slug')->all();

        $validated = $request->validate([
            'pet_id' => ['required', 'exists:pets,id'],
            'type' => ['required', 'string', 'in:' . implode(',', $allowedTypes)],
            'title' => ['nullable', 'string', 'max:255'],
            'due_at' => ['required', 'date'],
        ]);

        $reminder = Reminder::create([
            'pet_id' => $validated['pet_id'],
            'type' => $validated['type'],
            'title' => $validated['title'] ?? null,
            'due_at' => $validated['due_at'],
            'created_by' => $request->user()->id,
        ]);

        $reminderType = ReminderType::where('slug', $validated['type'])->first();

        if ($reminderType && $reminderType->show_in_ajanvaraus_calendar) {
            $dueAt = \Carbon\Carbon::parse($validated['due_at']);

            Event::dispatch(new ExternalTimeBlocked(
                \App\Models\Company::where('industry', 'kurssit')->value('id'),
                $dueAt->copy(),
                $dueAt->format('H:i:s'),
                $dueAt->copy()->addMinutes(30)->format('H:i:s'),
                'Lemmikkihoitola: muistutus (#'.$reminder->id.')',
                route('admin.pets.show', $validated['pet_id'], false)
            ));
        }

        if ($request->wantsJson()) {
            return response()->json($reminder, 201);
        }

        return redirect()
            ->route('admin.pets.show', $validated['pet_id'])
            ->with('status', 'Muistutus lisätty.');
    }

    public function toggle(Request $request, Reminder $reminder)
    {
        if ($reminder->isDone()) {
            $reminder->update([
                'done_at' => null,
                'done_by' => null,
            ]);
        } else {
            $reminder->update([
                'done_at' => now(),
                'done_by' => $request->user()->id,
            ]);
        }

        return response()->json([
            'done' => $reminder->isDone(),
        ]);
    }

    /**
     * Poistaa muistutuksen kokonaan eläinkortilta (AJAX, ei sivun
     * uudelleenlatausta, jotta vieritys pysyy paikallaan).
     */
    public function destroy(Reminder $reminder)
    {
        Event::dispatch(new ExternalTimeUnblocked(\App\Models\Company::where('industry', 'kurssit')->value('id'), 'Lemmikkihoitola: muistutus (#'.$reminder->id.')'));

        $reminder->delete();

        return response()->json(['deleted' => true]);
    }
}