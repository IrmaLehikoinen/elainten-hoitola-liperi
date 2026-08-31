<?php

namespace App\Modules\Kurssit\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Kurssit\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{
    public function index()
    {
        return view('kurssit::courses.index', [
            'courses' => Course::orderByDesc('starts_at')->get(),
        ]);
    }

    public function create()
    {
        return view('kurssit::courses.form', [
            'course' => new Course(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);

        $course = new Course($validated);
        $this->handleBrochure($request, $course);
        $course->save();

        return redirect()->route('kurssit.courses.index')->with('status', 'Kurssi tallennettu.');
    }

    public function edit(Course $course)
    {
        return view('kurssit::courses.form', [
            'course' => $course,
        ]);
    }

    public function update(Request $request, Course $course)
    {
        $validated = $this->validated($request);

        $course->fill($validated);
        $this->handleBrochure($request, $course);
        $course->save();

        return redirect()->route('kurssit.courses.index')->with('status', 'Kurssi päivitetty.');
    }

    public function destroy(Course $course)
    {
        if ($course->brochure_path) {
            Storage::disk('public')->delete($course->brochure_path);
        }

        $course->delete();

        return redirect()->route('kurssit.courses.index')->with('status', 'Kurssi poistettu.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'presentation_type' => ['required', 'in:text,brochure'],
            'description_html' => ['nullable', 'string'],
            'starts_at' => ['nullable', 'date'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'max_participants' => ['required', 'integer', 'min:0'],
            'brochure' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ]);
    }

    private function handleBrochure(Request $request, Course $course): void
    {
        if ($course->presentation_type !== 'brochure' || ! $request->hasFile('brochure')) {
            return;
        }

        if ($course->brochure_path) {
            Storage::disk('public')->delete($course->brochure_path);
        }

        $course->brochure_path = $request->file('brochure')->store('kurssit/esitteet', 'public');
    }
}