<?php

namespace App\Modules\Kurssit\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Kurssit\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{
    public function dashboard()
    {
        return view('kurssit::dashboard', [
            'courses' => Course::where(function ($query) {
                    $query->whereNull('starts_at')
                        ->orWhere('starts_at', '>=', now());
                })
                ->orderBy('starts_at')
                ->get(),
        ]);
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
        $this->handleContentBlocks($request, $course);
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
            'short_description' => ['required', 'string', 'max:500'],
            'presentation_type' => ['required', 'in:none,brochure,blocks'],
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