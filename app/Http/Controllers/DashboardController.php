<?php

namespace App\Http\Controllers;

use App\Models\Hospitalization;

class DashboardController extends Controller
{
    public function index()
    {
        $hospitalizations = Hospitalization::with('patient')
            ->orderByRaw("CASE status WHEN 'active' THEN 0 WHEN 'transferred' THEN 1 ELSE 2 END")
            ->orderBy('bed_number')
            ->get();

        $stats = [
            'active'      => $hospitalizations->where('status', 'active')->count(),
            'transferred' => $hospitalizations->where('status', 'transferred')->count(),
            'deceased'    => $hospitalizations->where('status', 'deceased')->count(),
        ];

        return view('dashboard', compact('hospitalizations', 'stats'));
    }
}
