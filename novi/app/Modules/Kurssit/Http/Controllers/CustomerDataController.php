<?php

namespace App\Modules\Kurssit\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Kurssit\Models\CourseRegistration;
use App\Modules\Kurssit\Models\GiftCard;
use Illuminate\Http\Request;

class CustomerDataController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('haku', ''));
        $registrations = collect();
        $giftCards = collect();
        $searched = $search !== '';

                if ($searched) {
            $like = '%'.$search.'%';

            $registrations = CourseRegistration::with('course')
                ->where(function ($query) use ($like) {
                    $query->where('name', 'like', $like)
                        ->orWhere('email', 'like', $like)
                        ->orWhere('phone', 'like', $like);
                })
                ->orderByDesc('created_at')
                ->get();

            $giftCards = GiftCard::where(function ($query) use ($like) {
                    $query->where('purchaser_name', 'like', $like)
                        ->orWhere('purchaser_email', 'like', $like);
                })
                ->orderByDesc('created_at')
                ->get();
        }

        return view('kurssit::customer-data.search', [
            'search' => $search,
            'searched' => $searched,
            'registrations' => $registrations,
            'giftCards' => $giftCards,
        ]);
    }
}