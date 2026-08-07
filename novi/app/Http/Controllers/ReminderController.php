<?php

namespace App\Http\Controllers;

use App\Models\Reminder;
use Illuminate\Http\Request;

class ReminderController extends Controller
{
    /**
     * Lisää uuden muistutuksen eläinkortin "Hoitojakson muistutukset"
     * -osiosta. Sama rivi näkyy automaattisesti myös kalenterissa ja
     * etusivun "Tänään huomioitavaa" -listalla, koska kaikki lukevat
     * samaa reminders-taulua.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pet_id' => ['required', 'exists:pets,id'],
            'type' => ['required', 'string', 'in:medication,feeding,wash,nails,vet,walk,other'],
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
}