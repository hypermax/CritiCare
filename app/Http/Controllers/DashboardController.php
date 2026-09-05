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

        $statusColors = [
            'active'      => 'bg-green-100 text-green-800 border-green-300',
            'deceased'    => 'bg-red-100 text-red-800 border-red-300',
            'transferred' => 'bg-blue-100 text-blue-800 border-blue-300',
        ];

        $rowColors = [
            'active'      => 'bg-green-50',
            'deceased'    => 'bg-red-50',
            'transferred' => 'bg-blue-50',
        ];

        return view('dashboard', compact('hospitalizations', 'stats', 'statusColors', 'rowColors'));
    }
}
