<?php

namespace App\Modules\Lemmikkihoitola\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Lemmikkihoitola\Models\Reminder;
use App\Modules\Lemmikkihoitola\Models\ReminderType;
use Illuminate\Http\Request;

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

        Reminder::create([
            'pet_id' => $validated['pet_id'],
            'type' => $validated['type'],
            'title' => $validated['title'] ?? null,
            'due_at' => $validated['due_at'],
            'created_by' => $request->user()->id,
        ]);

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
        $reminder->delete();

        return response()->json(['deleted' => true]);
    }
}