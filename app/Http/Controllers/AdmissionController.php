<?php

namespace App\Http\Controllers;

use App\Models\Hospitalization;
use App\Models\Patient;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdmissionController extends Controller
{
    public function create()
    {
        $occupiedBeds = Hospitalization::where('status', 'active')
            ->pluck('bed_number')
            ->toArray();

        return view('admissions.create', [
            'occupiedBeds' => $occupiedBeds,
            'bedCount'     => Setting::nbBeds(),
            'services'     => Setting::services(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'record_number'       => 'required|string|max:50',
            'first_name'          => 'required|string|max:100',
            'last_name'           => 'required|string|max:100',
            'birth_date'          => 'required|date|before:today',
            'sex_category'        => 'required|in:M,F,X',
            'phone'               => 'nullable|string|max:20',
            'bed_number'          => 'required|integer|min:1|max:'.Setting::nbBeds(),
            'admission_diagnosis' => 'nullable|string|max:255',
            'admission_source'    => 'nullable|string|max:100',
        ]);

        // Garde-fou : un patient décédé lors d'un séjour précédent ne peut pas être réadmis
        $existingPatient = Patient::where('record_number', $validated['record_number'])->first();

        if ($existingPatient && $existingPatient->hospitalizations()->where('status', 'deceased')->exists()) {
            return back()
                ->withErrors(['record_number' => 'Admission impossible : ce patient est décédé lors d’un séjour précédent. Vérifiez l’IPP.'])
                ->withInput();
        }

        // Garde-fou : un patient déjà hospitalisé ne peut pas avoir deux séjours actifs simultanés
        if ($existingPatient) {
            $activeStay = $existingPatient->hospitalizations()->where('status', 'active')->first();

            if ($activeStay) {
                return back()
                    ->withErrors(['record_number' => 'Admission impossible : ce patient a déjà un séjour en cours (lit n° ' . $activeStay->bed_number . '). Clôturez d’abord ce séjour.'])
                    ->withInput();
            }
        }

        $bedTaken = Hospitalization::where('bed_number', $validated['bed_number'])
            ->where('status', 'active')
            ->exists();

        if ($bedTaken) {
            return back()
                ->withErrors(['bed_number' => 'Ce lit est déjà occupé par un patient hospitalisé.'])
                ->withInput();
        }

        DB::transaction(function () use ($validated) {
            $patient = Patient::firstOrCreate(
                ['record_number' => $validated['record_number']],
                [
                    'first_name'   => $validated['first_name'],
                    'last_name'    => $validated['last_name'],
                    'birth_date'   => $validated['birth_date'],
                    'sex_category' => $validated['sex_category'],
                    'phone'        => $validated['phone'] ?? null,
                ]
            );

            Hospitalization::create([
                'patient_id'          => $patient->id,
                'bed_number'          => $validated['bed_number'],
                'admission_dttm'      => now(),
                'admission_diagnosis' => $validated['admission_diagnosis'] ?? null,
                'admission_source'    => $validated['admission_source'] ?? null,
                'status'              => 'active',
                'created_by'          => auth()->id(),
            ]);
        });

        return redirect()->route('dashboard')
            ->with('success', 'Patient admis avec succès au lit ' . $validated['bed_number'] . '.');
    }
}
