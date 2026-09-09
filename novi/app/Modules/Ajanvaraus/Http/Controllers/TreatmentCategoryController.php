<?php

namespace App\Modules\Ajanvaraus\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Ajanvaraus\Models\TreatmentCategory;
use Illuminate\Http\Request;

class TreatmentCategoryController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'link_url' => ['nullable', 'string', 'max:255'],
            'order' => ['nullable', 'integer', 'min:0'],
        ]);

        TreatmentCategory::create($validated);

        return back()->with('status', 'Otsikko lisätty.');
    }

    public function update(Request $request, TreatmentCategory $category)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'link_url' => ['nullable', 'string', 'max:255'],
            'order' => ['nullable', 'integer', 'min:0'],
        ]);

        $category->update($validated);

        return back()->with('status', 'Otsikko päivitetty.');
    }

    public function destroy(TreatmentCategory $category)
    {
        $category->delete();

        return back()->with('status', 'Otsikko poistettu. Sen alla olleet hoidot jäivät otsikoimattomiin.');
    }
}