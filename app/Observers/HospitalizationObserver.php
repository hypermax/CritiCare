<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\Hospitalization;

class HospitalizationObserver
{
    public function created(Hospitalization $hospitalization): void
    {
        AuditLog::record(
            'admission.created',
            "Admission de {$hospitalization->patient->full_name} — lit {$hospitalization->bed_number} ({$hospitalization->admission_source})",
            $hospitalization,
            [
                'ipp' => $hospitalization->patient->record_number,
                'lit' => $hospitalization->bed_number,
                'provenance' => $hospitalization->admission_source,
            ]
        );
    }

    public function updated(Hospitalization $hospitalization): void
    {
        // On ne trace que les changements de statut (= clôture du séjour)
        if (! $hospitalization->wasChanged('status')) {
            return;
        }

        $isDeceased = $hospitalization->status === 'deceased';

        AuditLog::record(
            $isDeceased ? 'discharge.deceased' : 'discharge.transferred',
            ($isDeceased ? 'Décès' : "Transfert vers {$hospitalization->discharge_destination}")
                ." — {$hospitalization->patient->full_name} (lit {$hospitalization->bed_number})",
            $hospitalization,
            [
                'ipp' => $hospitalization->patient->record_number,
                'lit' => $hospitalization->bed_number,
                'destination' => $hospitalization->discharge_destination,
                'duree_jours' => (int) $hospitalization->admission_dttm
                    ->diffInDays($hospitalization->discharge_dttm ?? now()) + 1,
            ]
        );
    }
}
