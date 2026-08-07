<?php

namespace App\Http\Controllers;

use App\Models\ReadingPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReadingPlanController extends Controller
{
    public function index(Request $request)
    {
        $query = ReadingPlan::query()
            ->where('user_id', Auth::id());

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $readingPlans = $query->orderBy('target_date', 'asc')
            ->paginate(10)
            ->withQueryString();

        return view('reading-plans.index', compact('readingPlans'));
    }
}
