<?php

namespace App\Providers;

use App\Models\AuditLog;
use App\Models\Hospitalization;
use App\Models\Patient;
use App\Models\User;
use App\Observers\HospitalizationObserver;
use App\Observers\PatientObserver;
use App\Observers\UserObserver;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Journal d'audit : observeurs des modèles métier
        Patient::observe(PatientObserver::class);
        Hospitalization::observe(HospitalizationObserver::class);
        User::observe(UserObserver::class);

        // Journal d'audit : connexions / déconnexions
        Event::listen(Login::class, function (Login $event): void {
            AuditLog::record('auth.login', "Connexion : {$event->user->email}", null, [], $event->user->id);
        });

        Event::listen(Logout::class, function (Logout $event): void {
            if ($event->user) {
                AuditLog::record('auth.logout', "Déconnexion : {$event->user->email}", null, [], $event->user->id);
            }
        });
    }
}
