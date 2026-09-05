<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\Patient;

class PatientObserver
{
    public function created(Patient $patient): void
    {
        AuditLog::record(
            'patient.created',
            "Nouveau patient : {$patient->full_name} (IPP {$patient->record_number})",
            $patient,
            ['ipp' => $patient->record_number, 'sexe' => $patient->sex_category]
        );
    }
}
