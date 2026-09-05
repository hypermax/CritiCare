<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\User;

class UserObserver
{
    public function created(User $user): void
    {
        AuditLog::record(
            'user.created',
            "Compte créé : {$user->name} ({$user->email}) — rôle ".($user->role->code ?? '—'),
            $user,
            ['email' => $user->email, 'role' => $user->role->code ?? null]
        );
    }

    public function updated(User $user): void
    {
        if ($user->wasChanged('role_id')) {
            AuditLog::record(
                'user.role_changed',
                "Rôle modifié pour {$user->name} : ".($user->role->code ?? '—'),
                $user,
                ['email' => $user->email, 'nouveau_role' => $user->role->code ?? null]
            );
        }

        if ($user->wasChanged('password')) {
            // Jamais de hash ni de mot de passe dans le journal
            AuditLog::record(
                'user.password_reset',
                "Mot de passe réinitialisé : {$user->email}",
                $user,
                ['email' => $user->email]
            );
        }
    }
}
