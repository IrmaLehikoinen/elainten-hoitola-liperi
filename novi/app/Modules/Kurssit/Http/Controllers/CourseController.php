<?php

namespace App\Modules\Kurssit\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Events\CheckRecurringConflict;
use App\Events\CollectExternalCalendarEntries;
use App\Modules\Kurssit\Mail\CourseCancelled;
use App\Modules\Kurssit\Models\Course;
use App\Modules\Kurssit\Models\CourseReminder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{
    public function dashboard()
    {
        $today = Carbon::today();

        $calendarAnchor = request('date')
            ? Carbon::createFromFormat('Y-m-d', request('date'))->startOfDay()
            : $today->copy();
        $monthStart = $calendarAnchor->copy()->startOfMonth();
        $monthEnd = $calendarAnchor->copy()->endOfMonth();

        $upcoming = Course::where(function ($query) {
                $query->whereNull('starts_at')
                    ->orWhere('starts_at', '>=', now());
            })
            ->orderBy('starts_at')
            ->get();

        $thisMonthCourses = $upcoming->filter(
            fn ($course) => $course->starts_at && $course->starts_at->between($monthStart, $monthEnd)
        )->values();

        $otherUpcomingCourses = $upcoming->reject(
            fn ($course) => $thisMonthCourses->contains('id', $course->id)
        )->values();

        $reminders = CourseReminder::with('course')
            ->open()
            ->orderBy('due_at')
            ->get()
            ->map(function ($reminder) {
                return [
                    'id' => $reminder->id,
                    'title' => $reminder->title,
                    'due_at' => $reminder->due_at?->format('d.m.Y'),
                    'course_label' => $reminder->course
                        ? $reminder->course->name.($reminder->course->starts_at ? ' · '.$reminder->course->starts_at->format('d.m.') : '')
                        : null,
                ];
            });

        return view('kurssit::dashboard', [
            'thisMonthCourses' => $thisMonthCourses,
            'otherUpcomingCourses' => $otherUpcomingCourses,
            'course' => new Course(),
            'calendarMonth' => $monthStart,
            'calendarDays' => $this->buildCalendarDays($monthStart),
            'reminders' => $reminders,
            'upcomingReminders' => $this->buildUpcomingReminderNotes(),
        ]);
    }

    /**
     * Hoitoaikojen ja kurssien omat "muistutus"-tekstikentät (eri asia kuin
     * yllä oleva CourseReminder-lista) — nousee näkyviin Etusivun paneeliin
     * viikkoa ennen sitä päivää kun aika/kurssi on.
     */
    private function buildUpcomingReminderNotes(): \Illuminate\Support\Collection
    {
        $today = Carbon::today();
        $weekAhead = $today->copy()->addDays(7);
        $entries = collect();

        Course::whereNotNull('reminder_note')
            ->where('reminder_note', '!=', '')
            ->whereNotNull('reminder_date')
            ->whereBetween('reminder_date', [$today, $weekAhead])
            ->get()
            ->each(function ($course) use ($entries) {
                $entries->push([
                    'type' => 'course',
                    'id' => $course->id,
                    'label' => $course->name,
                    'date' => $course->reminder_date,
                    'note' => $course->reminder_note,
                    'edit_url' => route('kurssit.courses.edit', $course->id),
                ]);
            });

        \App\Modules\Ajanvaraus\Models\Treatment::with(['availabilityRules', 'specialOpenings'])
            ->get()
            ->each(function ($treatment) use ($entries, $today, $weekAhead) {
                foreach ($treatment->availabilityRules as $rule) {
                    if (! $rule->reminder_note || ! $rule->reminder_date) {
                        continue;
                    }

                    $date = \Illuminate\Support\Carbon::parse($rule->reminder_date);

                    if ($date->lt($today) || $date->gt($weekAhead)) {
                        continue;
                    }

                    $entries->push([
                        'type' => 'availability_rule',
                        'id' => $rule->id,
                        'label' => $treatment->name,
                        'date' => $date,
                        'note' => $rule->reminder_note,
                        'edit_url' => route('ajanvaraus.treatments.edit', $treatment->id),
                    ]);
                }

                foreach ($treatment->specialOpenings as $opening) {
                    if (! $opening->reminder_note || ! $opening->reminder_date) {
                        continue;
                    }

                    $date = \Illuminate\Support\Carbon::parse($opening->reminder_date);

                    if ($date->lt($today) || $date->gt($weekAhead)) {
                        continue;
                    }

                    $entries->push([
                        'type' => 'special_opening',
                        'id' => $opening->id,
                        'label' => $treatment->name,
                        'date' => $date,
                        'note' => $opening->reminder_note,
                        'edit_url' => route('ajanvaraus.treatments.edit', $treatment->id),
                    ]);
                }
            });

        return $entries->sortBy('date')->values();
    }

    /**
     * Kuittaa yhden viikkonäkymän muistutuksen tehdyksi — tyhjentää
     * reminder_note/reminder_date kyseiseltä riviltä, jolloin se ei
     * enää näy "Muistutukset (viikon sisällä)" -listalla.
     */
    public function dismissReminder(Request $request)
    {
        $validated = $request->validate([
            'type' => ['required', 'in:course,availability_rule,special_opening'],
            'id' => ['required', 'integer'],
        ]);

        $clear = ['reminder_note' => null, 'reminder_date' => null];

        match ($validated['type']) {
            'course' => Course::where('id', $validated['id'])->update($clear),
            'availability_rule' => \App\Modules\Ajanvaraus\Models\TreatmentAvailabilityRule::where('id', $validated['id'])->update($clear),
            'special_opening' => \App\Modules\Ajanvaraus\Models\TreatmentSpecialOpening::where('id', $validated['id'])->update($clear),
        };

        return response()->json(['done' => true]);
    }

       private function buildCalendarDays(Carbon $monthStart)
    {
        $start = $monthStart->copy()->startOfMonth();
        $end = $monthStart->copy()->endOfMonth();

        $courses = Course::whereNotNull('starts_at')
            ->get()
            ->filter(fn ($course) => $course->starts_at->between($start, $end));

        $event = new CollectExternalCalendarEntries($start, $end->copy()->endOfDay(), 'kurssit');
        Event::dispatch($event);

        $blocks = \App\Modules\Ajanvaraus\Models\CalendarBlock::whereBetween('date', [$start->toDateString(), $end->toDateString()])->get();

        $days = [];

        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $dayCourses = $courses->filter(fn ($c) => $c->starts_at->isSameDay($date))->values()->map(function ($course) {
                $title = $course->name.' klo '.$course->starts_at->format('H:i');

                if ($course->ends_at) {
                    $title .= '–'.$course->ends_at->format('H:i');
                }

                return [
                    'title' => $title,
                    'color' => '#C98FA8',
                    'filled' => true,
                ];
            });

            $external = collect($event->entries)->filter(fn ($e) => $e['date'] === $date->format('Y-m-d'))->values();

            $dayEntries = $dayCourses->concat($external);

            $blocks
                ->filter(fn ($block) => $block->date->isSameDay($date))
                ->groupBy(function ($block) {
                    $label = preg_replace('/\s*\(varaus #\d+\)\s*$/', '', $block->reason ?: 'Ei vapaita aikoja');

                    return $label.'|'.$block->start_time.'|'.$block->end_time;
                })
                ->each(function ($group) use ($dayEntries) {
                    $first = $group->first();
                    $title = preg_replace('/\s*\(varaus #\d+\)\s*$/', '', $first->reason ?: 'Ei vapaita aikoja');

                    if ($first->start_time) {
                        $title .= ' klo '.substr($first->start_time, 0, 5);

                        if ($first->end_time && $first->end_time !== $first->start_time) {
                            $title .= '–'.substr($first->end_time, 0, 5);
                        }
                    }

                    if ($group->count() > 1) {
                        $title .= ' ('.$group->count().')';
                    }

                    $dayEntries->push([
                        'title' => $title,
                        'color' => '#3F4F3A',
                        'filled' => true,
                    ]);
                });

            $days[] = [
                'date' => $date->copy(),
                'entries' => $dayEntries->sortBy(function ($entry) {
                    if (preg_match('/(\d{2}:\d{2})/', $entry['title'], $m)) {
                        return $m[1];
                    }

                    return '99:99';
                })->values(),
            ];
        }

        return $days;
    }

    public function index()
    {
        return view('kurssit::courses.index', [
            'courses' => Course::orderByDesc('starts_at')->get(),
            'course' => new Course(),
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
        $this->handleContentBlocks($request, $course);
        $course->save();

        $this->handleReminder($request, $course);

        $redirect = redirect()->route('kurssit.courses.index')->with('status', 'Kurssi tallennettu.');
        $warning = $this->checkScheduleConflict($course);

        if ($warning) {
            $redirect->with('schedule_warning', $warning);
        }

        return $redirect;
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
        $this->handleContentBlocks($request, $course);
        $course->save();

        $this->handleReminder($request, $course);

        $redirect = redirect()->route('kurssit.courses.index')->with('status', 'Kurssi päivitetty.');
        $warning = $this->checkScheduleConflict($course);

        if ($warning) {
            $redirect->with('schedule_warning', $warning);
        }

        return $redirect;
    }

        public function destroy(Course $course)
    {
        if ($course->brochure_path) {
            Storage::disk('public')->delete($course->brochure_path);
        }

        $course->delete();

        return redirect()->route('kurssit.courses.index')->with('status', 'Kurssi poistettu.');
    }

    /**
     * Kopioi kurssin perustiedot uudeksi kurssiksi. Esite ja kuvalohkot
     * jätetään tarkoituksella tyhjiksi — jos ne kopioitaisiin sellaisenaan,
     * yhden kopion kuvan vaihtaminen poistaisi tiedoston myös toiselta
     * kurssilta (sama tiedostopolku, ks. handleContentBlocks/handleBrochure).
     */
    public function duplicate(Course $course)
    {
        $copy = Course::create([
            'company_id' => $course->company_id,
            'name' => $course->name.' (kopio)',
            'short_description' => $course->short_description,
            'presentation_type' => 'none',
            'price' => $course->price,
            'max_participants' => $course->max_participants,
        ]);

        return redirect()->route('kurssit.courses.edit', $copy)
            ->with('status', 'Kurssi kopioitu — täydennä ajankohta ja muut tiedot.');
    }

    public function toggleRegistrationClosed(Course $course)
    {
        $course->update([
            'registration_closed_at' => $course->registration_closed_at ? null : now(),
        ]);

        return back()->with('status', $course->registration_closed_at
            ? 'Ilmoittautuminen suljettu.'
            : 'Ilmoittautuminen avattu.');
    }

           public function toggleCancelled(Course $course)
    {
        if ($course->cancelled_at) {
            $course->update(['cancelled_at' => null]);

            return back()->with('status', 'Peruutus poistettu.');
        }

                $course->update(['cancelled_at' => now()]);

        $registrations = $course->registrations()->where('status', '!=', 'cancelled')->get();

        foreach ($registrations as $registration) {
            $registration->update([
                'status' => 'cancelled',
                'cancellation_reason' => 'course_cancelled',
            ]);

            if ($registration->email) {
                Mail::to($registration->email)->send(new CourseCancelled($registration));
            }
        }

        return back()->with('status', 'Kurssi merkitty peruutetuksi ja osallistujille lähetetty viesti.');
    } 

        private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'short_description' => ['required', 'string', 'max:500'],
            'presentation_type' => ['required', 'in:none,brochure,blocks'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after:starts_at'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'max_participants' => ['required', 'integer', 'min:0'],
             'brochure' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
            'reminder_note' => ['nullable', 'string', 'max:2000'],
            'reminder_date' => ['nullable', 'date'],
        ]);
    } 
    /**
     * Kysyy Ajanvaraus-moduulilta (jos sillä yrityksellä on se käytössä)
     * osuuko tämä kurssin ajankohta jonkin viikoittain toistuvan hoidon
     * (esim. "joka sunnuntai" jooga) päälle. Ei estä tallennusta, vain
     * varoittaa — yrittäjä päättää itse miten toimii.
     */
    private function checkScheduleConflict(Course $course): ?string
    {
        if (! $course->starts_at || ! $course->ends_at) {
            return null;
        }

        $event = new CheckRecurringConflict($course->company_id, $course->starts_at->copy(), $course->ends_at->copy());
        Event::dispatch($event);

        if (empty($event->conflicts)) {
            return null;
        }

        return 'Huomio: samaan aikaan on jo varattavissa: '.implode(', ', $event->conflicts).'. Tarkista ettei mene päällekkäin.';
    }

    /**
     * Jos lomakkeella kirjoitettiin muistutusteksti, luodaan siitä
     * automaattisesti kurssiin linkitetty CourseReminder-rivi.
     */
    private function handleReminder(Request $request, Course $course): void
    {
        if (! $request->filled('reminder_title')) {
            return;
        }

        CourseReminder::create([
            'course_id' => $course->id,
            'title' => $request->input('reminder_title'),
            'due_at' => $request->input('reminder_due_at') ?: null,
            'created_by' => $request->user()->id,
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
        $this->optimizeImage($course->brochure_path);
    }

    /**
     * Lukee lomakkeen "blocks"-taulukon (Alpine.js:n kokoamat esittelysivun
     * lohkot) ja tallentaa sen Course::content_blocks-kenttään JSON-muodossa.
     * Vain tunnistetut lohkotyypit hyväksytään — kaikki muu hylätään.
     */
    private function handleContentBlocks(Request $request, Course $course): void
    {
        if ($course->presentation_type !== 'blocks') {
            $course->content_blocks = null;
            return;
        }

        $blocks = $request->input('blocks', []);
        $result = [];

        foreach ($blocks as $block) {
            $type = $block['type'] ?? null;
            $uid = $block['uid'] ?? null;

            if (in_array($type, ['heading', 'subheading', 'paragraph'], true)) {
                $text = trim($block['text'] ?? '');

                if ($text !== '') {
                    $result[] = ['type' => $type, 'text' => $text];
                }
            } elseif (in_array($type, ['image_full', 'image_side'], true)) {
                $path = $block['path'] ?? null;

                if ($uid && $request->hasFile('block_image_'.$uid)) {
                    $file = $request->file('block_image_'.$uid);

                    if ($file->isValid()
                        && in_array(strtolower($file->getClientOriginalExtension()), ['jpg', 'jpeg', 'png'])
                        && $file->getSize() <= 10 * 1024 * 1024) {
                        if ($path) {
                            Storage::disk('public')->delete($path);
                        }
                        $path = $file->store('kurssit/lohkot', 'public');
                        $this->optimizeImage($path);
                    }
                }

                if (! $path) {
                    continue;
                }

                $entry = ['type' => $type, 'path' => $path];

                if ($type === 'image_side') {
                    $entry['align'] = ($block['align'] ?? 'left') === 'right' ? 'right' : 'left';
                    $entry['text'] = trim($block['text'] ?? '');
                }

                $result[] = $entry;
            } elseif ($type === 'checklist') {
                $items = collect(explode("\n", $block['items_text'] ?? ''))
                    ->map(fn ($item) => trim($item))
                    ->filter()
                    ->values()
                    ->all();

                if ($items) {
                    $result[] = ['type' => 'checklist', 'items' => $items];
                }
            }
        }

        $course->content_blocks = $result;
    }

    /**
     * Pienentää ja pakkaa ladatun kuvan automaattisesti, jottei sivusto
     * hidastu isoista kuvista. Ei koske PDF-tiedostoja. Jos kuva on jo
     * kohtuukokoinen, sitä ei suurenneta tai muuteta.
     */
    private function optimizeImage(string $relativePath): void
    {
        $fullPath = Storage::disk('public')->path($relativePath);
        $extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));

        if (! in_array($extension, ['jpg', 'jpeg', 'png'])) {
            return;
        }

        $imageInfo = @getimagesize($fullPath);

        if (! $imageInfo) {
            return;
        }

        [$width, $height] = $imageInfo;
        $maxWidth = 1600;

        if ($width <= $maxWidth) {
            return;
        }

        $source = $extension === 'png' ? imagecreatefrompng($fullPath) : imagecreatefromjpeg($fullPath);

        if (! $source) {
            return;
        }

        $newWidth = $maxWidth;
        $newHeight = (int) round($height * ($maxWidth / $width));

        $resized = imagecreatetruecolor($newWidth, $newHeight);

        if ($extension === 'png') {
            imagealphablending($resized, false);
            imagesavealpha($resized, true);
        }

        imagecopyresampled($resized, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        if ($extension === 'png') {
            imagepng($resized, $fullPath, 6);
        } else {
            imagejpeg($resized, $fullPath, 82);
        }

        imagedestroy($source);
        imagedestroy($resized);
    }
}