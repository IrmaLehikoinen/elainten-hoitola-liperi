<?php

namespace App\Modules\Kurssit\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Kurssit\Models\CourseReminder;
use Illuminate\Http\Request;

class CourseReminderController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'due_at' => ['nullable', 'date'],
            'course_id' => ['nullable', 'exists:courses,id'],
        ]);

        CourseReminder::create([
            'title' => $validated['title'],
            'due_at' => $validated['due_at'] ?? null,
            'course_id' => $validated['course_id'] ?? null,
            'created_by' => $request->user()->id,
        ]);

        return redirect()->route('kurssit.dashboard')->with('status', 'Muistutus lisätty.');
    }

    public function toggle(Request $request, CourseReminder $reminder)
    {
        if ($reminder->isDone()) {
            $reminder->update(['done_at' => null, 'done_by' => null]);
        } else {
            $reminder->update(['done_at' => now(), 'done_by' => $request->user()->id]);
        }

        return response()->json(['done' => $reminder->isDone()]);
    }

    public function destroy(CourseReminder $reminder)
    {
        $reminder->delete();

        return response()->json(['deleted' => true]);
    }
}