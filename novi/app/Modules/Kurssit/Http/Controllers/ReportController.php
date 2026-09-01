<?php

namespace App\Modules\Kurssit\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Kurssit\Models\Course;
use App\Modules\Kurssit\Models\CourseRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->get('q'));
        $participantSearch = trim((string) $request->get('participant'));

        $courses = new Collection();

        if ($search !== '') {
            $courses = Course::where('starts_at', '<', now())
                ->where('name', 'like', "%{$search}%")
                ->orderByDesc('starts_at')
                ->withCount(['registrations as confirmed_count' => function ($query) {
                    $query->where('status', 'confirmed');
                }])
                ->get();
        }

        $participantRegistrations = new Collection();

        if ($participantSearch !== '') {
            $participantRegistrations = CourseRegistration::with('course')
                ->where('name', 'like', "%{$participantSearch}%")
                ->orderByDesc('created_at')
                ->get();
        }

        return view('kurssit::reports.index', [
            'courses' => $courses,
            'search' => $search,
            'participantRegistrations' => $participantRegistrations,
            'participantSearch' => $participantSearch,
        ]);
    }
}