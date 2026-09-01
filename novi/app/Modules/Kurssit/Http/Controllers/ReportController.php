<?php

namespace App\Modules\Kurssit\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Kurssit\Models\Course;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->get('q'));

        $courses = Course::where('starts_at', '<', now())
            ->when($search !== '', fn ($query) => $query->where('name', 'like', "%{$search}%"))
            ->orderByDesc('starts_at')
            ->withCount(['registrations as confirmed_count' => function ($query) {
                $query->where('status', 'confirmed');
            }])
            ->get();

        return view('kurssit::reports.index', [
            'courses' => $courses,
            'search' => $search,
        ]);
    }
}