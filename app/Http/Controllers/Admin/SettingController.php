<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    /**
     * Paramètres du service — réservés au rôle ADMIN
     * (middleware role:ADMIN appliqué dans routes/web.php).
     */
    public function edit(): View
    {
        return view('admin.settings.edit', [
            'hospitalName' => Setting::get('hospital_name', ''),
            'serviceName'  => Setting::get('service_name', ''),
            'nbBeds'       => Setting::nbBeds(),
            'services'     => implode("\n", Setting::services()),
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'hospital_name' => ['nullable', 'string', 'max:100'],
            'service_name'  => ['nullable', 'string', 'max:100'],
            'nb_beds'       => ['required', 'integer', 'min:1', 'max:100'],
            'services'      => ['required', 'string'],
        ]);

        // Normalise la liste : une ligne = un service, lignes vides et doublons ignorés
        $services = collect(preg_split('/\r?\n/', $data['services']))
            ->map(fn ($line) => trim($line))
            ->filter()
            ->unique()
            ->values();

        if ($services->isEmpty()) {
            return back()
                ->withErrors(['services' => 'La liste doit contenir au moins un service.'])
                ->withInput();
        }

        Setting::set('hospital_name', $data['hospital_name']);
        Setting::set('service_name', $data['service_name']);
        Setting::set('nb_beds', $data['nb_beds']);
        Setting::set('services', $services->implode("\n"));

        AuditLog::record('settings.updated', 'Paramètres du service modifiés', null, [
            'nb_lits' => (int) $data['nb_beds'],
            'nb_services' => $services->count(),
        ]);

        return redirect()->route('admin.settings.edit')
            ->with('success', 'Paramètres enregistrés.');
    }
}
