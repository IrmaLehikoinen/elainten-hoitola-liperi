<?php

namespace App\Http\Controllers;

use App\Models\Reminder;
use Illuminate\Http\Request;

class ReminderController extends Controller
{
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