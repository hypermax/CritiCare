<?php

namespace App\Http\Controllers;

use App\Models\Hospitalization;
use Illuminate\Http\Request;

class DischargeController extends Controller
{
    public function edit(Hospitalization $hospitalization)
    {
        if ($hospitalization->status !== 'active') {
            return redirect()->route('dashboard')
                ->with('error', 'Cette hospitalisation est déjà clôturée.');
        }

        return view('discharges.edit', compact('hospitalization'));
    }

    public function update(Request $request, Hospitalization $hospitalization)
    {
        if ($hospitalization->status !== 'active') {
            return redirect()->route('dashboard')
                ->with('error', 'Cette hospitalisation est déjà clôturée.');
        }

        $validated = $request->validate([
            'outcome'               => 'required|in:transferred,deceased',
            'discharge_destination' => 'required_if:outcome,transferred|nullable|string|max:100',
            'death_cause'           => 'required_if:outcome,deceased|nullable|string|max:500',
        ]);

        if ($validated['outcome'] === 'deceased' && ! $request->user()->hasAnyRole(['ADMIN', 'SENIOR'])) {
            return back()
                ->withErrors(['outcome' => 'Seul un médecin senior (ou l’administrateur) peut constater un décès.'])
                ->withInput();
        }

        // Assignation explicite : fonctionne même si death_cause n'est pas dans $fillable
        $hospitalization->status = $validated['outcome'];
        $hospitalization->discharge_dttm = now();
        $hospitalization->discharge_destination = $validated['outcome'] === 'transferred'
            ? $validated['discharge_destination']
            : null;
        $hospitalization->death_cause = $validated['outcome'] === 'deceased'
            ? $validated['death_cause']
            : null;
        $hospitalization->save();

        $label = $validated['outcome'] === 'transferred' ? 'transféré' : 'décédé';

        return redirect()->route('dashboard')
            ->with('success', "Sortie enregistrée : {$hospitalization->patient->full_name} ({$label}).");
    }
}
