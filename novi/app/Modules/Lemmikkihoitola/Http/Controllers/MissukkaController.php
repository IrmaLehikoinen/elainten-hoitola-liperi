<?php

namespace App\Modules\Lemmikkihoitola\Http\Controllers;

use App\Http\Controllers\Controller;

class MissukkaController extends Controller
{
    public function index()
    {
        return view('public.missukka');
    }
}