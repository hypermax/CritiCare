<?php

namespace App\Http\Controllers;

use App\Models\Hospitalization;
use App\Models\Patient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PatientController extends Controller
{
    /**
     * Archives & recherche : retrouver un patient par IPP, nom ou prénom.
     * Sans recherche : affiche les 20 dernières sorties.
     * Accessible à tous les rôles (matrice : consultation = tous).
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));

        $results = null;
        $recentDischarges = null;

        if ($search !== '') {
            $term = '%'.addcslashes($search, '%_').'%'; // neutralise les jokers LIKE

            $results = Patient::query()
                ->where(function ($query) use ($term) {
                    $query->where('record_number', 'like', $term)
                        ->orWhere('last_name', 'like', $term)
                        ->orWhere('first_name', 'like', $term);
                })
                ->withCount('hospitalizations')
                ->with(['hospitalizations' => function ($query) {
                    $query->orderByDesc('admission_dttm')->limit(1);
                }])
                ->orderBy('last_name')
                ->orderBy('first_name')
                ->limit(50)
                ->get();
        } else {
            $recentDischarges = Hospitalization::with('patient')
                ->whereNotNull('discharge_dttm')
                ->orderByDesc('discharge_dttm')
                ->limit(20)
                ->get();
        }

        return view('patients.index', [
            'search' => $search,
            'results' => $results,
            'recentDischarges' => $recentDischarges,
        ]);
    }

    /**
     * Recherche d'un patient par IPP pour le préremplissage du formulaire
     * d'admission (appel AJAX JSON). Accessible à tous les rôles authentifiés.
     */
    public function lookup(Request $request): JsonResponse
    {
        $request->validate(['record_number' => 'required|string|max:50']);

        $patient = Patient::where('record_number', $request->query('record_number'))->first();

        if (! $patient) {
            return response()->json(['found' => false]);
        }

        return response()->json([
            'found'        => true,
            'first_name'   => $patient->first_name,
            'last_name'    => $patient->last_name,
            'birth_date'   => $patient->birth_date->format('Y-m-d'),
            'sex_category' => $patient->sex_category,
            'phone'        => $patient->phone,
            'deceased'     => $patient->hospitalizations()->where('status', 'deceased')->exists(),
            'fiche_url'    => route('patients.show', $patient),
        ]);
    }

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
