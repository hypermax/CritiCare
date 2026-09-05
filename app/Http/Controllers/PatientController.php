<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\View\View;

class PatientController extends Controller
{
    /**
     * Fiche patient : identité + historique complet des séjours.
     * Accessible à tous les rôles (matrice : consultation = tous).
     */
    public function show(Patient $patient): View
    {
        $patient->load(['hospitalizations' => function ($query) {
            $query->with('creator')->orderByDesc('admission_dttm');
        }]);

        $activeStay = $patient->hospitalizations->firstWhere('status', 'active');

        $totalDays = $patient->hospitalizations->sum(function ($stay) {
            $end = $stay->discharge_dttm ?? now();

            return (int) $stay->admission_dttm->diffInDays($end) + 1; // J1 = jour d'admission
        });

        return view('patients.show', [
            'patient' => $patient,
            'activeStay' => $activeStay,
            'totalDays' => $totalDays,
        ]);
    }
}
